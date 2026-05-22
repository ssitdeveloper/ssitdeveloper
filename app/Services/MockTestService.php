<?php

namespace App\Services;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Models\TestResult;
use App\Models\TestAnalytics;
use App\Models\TestBookmark;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MockTestService
{
    /**
     * Create a new test
     */
    public function createTest(array $data): Test
    {
        return DB::transaction(function () use ($data) {
            return Test::create($data);
        });
    }

    /**
     * Start a test attempt for a user
     */
    public function startTestAttempt(User $user, Test $test): TestAttempt
    {
        // Check if user already has an active attempt
        $existingAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return $existingAttempt;
        }

        // Check if test is available
        if (!$test->isAvailable()) {
            throw new \Exception('Test is not available for attempt');
        }

        // Generate randomized questions based on test configuration
        $questionIds = $this->generateQuestionIds($test);

        return DB::transaction(function () use ($user, $test, $questionIds) {
            return TestAttempt::create([
                'test_id' => $test->id,
                'user_id' => $user->id,
                'question_ids' => $questionIds,
                'started_at' => now(),
                'expires_at' => now()->addMinutes($test->duration_minutes),
                'status' => 'in_progress',
            ]);
        });
    }

    /**
     * Generate randomized question IDs based on test configuration
     */
    private function generateQuestionIds(Test $test): array
    {
        $questionIds = [];

        if ($test->subject_distribution) {
            // Distribute questions by subject
            foreach ($test->subject_distribution as $subject => $count) {
                $subjectQuestions = Question::published()
                    ->bySubject($this->getSubjectId($subject))
                    ->inRandomOrder()
                    ->limit($count)
                    ->pluck('id')
                    ->toArray();

                $questionIds = array_merge($questionIds, $subjectQuestions);
            }
        }

        // If we don't have enough questions, fill with random ones
        $remaining = $test->total_questions - count($questionIds);
        if ($remaining > 0) {
            $additionalQuestions = Question::published()
                ->whereNotIn('id', $questionIds)
                ->inRandomOrder()
                ->limit($remaining)
                ->pluck('id')
                ->toArray();

            $questionIds = array_merge($questionIds, $additionalQuestions);
        }

        // Shuffle final list
        shuffle($questionIds);

        return array_slice($questionIds, 0, $test->total_questions);
    }

    /**
     * Get subject ID by name
     */
    private function getSubjectId(string $subjectName): ?int
    {
        return \App\Models\Subject::where('name', 'ILIKE', $subjectName)->value('id');
    }

    /**
     * Save or update answer for a question
     */
    public function saveAnswer(TestAttempt $attempt, int $questionId, ?int $selectedOptionId, bool $markForReview = false): TestAttemptAnswer
    {
        if (!$attempt->isInProgress()) {
            throw new \Exception('Test attempt is not in progress');
        }

        if (!in_array($questionId, $attempt->question_ids)) {
            throw new \Exception('Question not part of this test attempt');
        }

        return DB::transaction(function () use ($attempt, $questionId, $selectedOptionId, $markForReview) {
            return TestAttemptAnswer::updateOrCreate(
                [
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'selected_option_id' => $selectedOptionId,
                    'is_marked_for_review' => $markForReview,
                    'answered_at' => now(),
                ]
            );
        });
    }

    /**
     * Submit test and calculate results
     */
    public function submitTest(TestAttempt $attempt): TestResult
    {
        if (!$attempt->isInProgress()) {
            throw new \Exception('Test attempt is not in progress');
        }

        return DB::transaction(function () use ($attempt) {
            // Mark attempt as completed
            $attempt->markAsCompleted();

            // Calculate results
            $result = $this->calculateResults($attempt);

            // Update analytics
            TestAnalytics::updateAnalyticsForTest($attempt->test_id);

            return $result;
        });
    }

    /**
     * Auto-submit expired tests
     */
    public function autoSubmitExpiredTests(): int
    {
        $expiredAttempts = TestAttempt::where('status', 'in_progress')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredAttempts as $attempt) {
            try {
                $this->submitTest($attempt);
                $count++;
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error('Failed to auto-submit test attempt: ' . $attempt->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $count;
    }

    /**
     * Calculate test results
     */
    private function calculateResults(TestAttempt $attempt): TestResult
    {
        $answers = $attempt->answers()->with(['question', 'selectedOption'])->get();
        $totalQuestions = count($attempt->question_ids);
        $attemptedQuestions = $answers->count();
        $unansweredQuestions = $totalQuestions - $attemptedQuestions;

        $correctAnswers = 0;
        $wrongAnswers = 0;
        $obtainedMarks = 0.0;
        $negativeMarks = 0.0;

        $subjectWise = [];
        $difficultyWise = [];
        $timeWise = [];

        foreach ($answers as $answer) {
            $question = $answer->question;
            $isCorrect = $answer->isCorrect();

            if ($isCorrect) {
                $correctAnswers++;
                $obtainedMarks += $attempt->test->marks_per_question;
            } else {
                $wrongAnswers++;
                $negativeMarks += $attempt->test->negative_marking;
            }

            // Subject-wise analysis
            $subject = $question->chapter->topic->subject->name;
            if (!isset($subjectWise[$subject])) {
                $subjectWise[$subject] = ['correct' => 0, 'wrong' => 0, 'total' => 0];
            }
            $subjectWise[$subject]['total']++;
            if ($isCorrect) {
                $subjectWise[$subject]['correct']++;
            } else {
                $subjectWise[$subject]['wrong']++;
            }

            // Difficulty-wise analysis
            $difficulty = $question->difficulty_level;
            if (!isset($difficultyWise[$difficulty])) {
                $difficultyWise[$difficulty] = ['correct' => 0, 'wrong' => 0, 'total' => 0];
            }
            $difficultyWise[$difficulty]['total']++;
            if ($isCorrect) {
                $difficultyWise[$difficulty]['correct']++;
            } else {
                $difficultyWise[$difficulty]['wrong']++;
            }
        }

        $totalMarks = $totalQuestions * $attempt->test->marks_per_question;
        $finalMarks = $obtainedMarks - $negativeMarks;
        $percentage = $totalQuestions > 0 ? round(($finalMarks / $totalMarks) * 100, 2) : 0;

        // Calculate percentile and rank
        $percentile = TestAnalytics::getPercentileForScore($attempt->test_id, $finalMarks);
        $rank = TestAnalytics::getRankForScore($attempt->test_id, $finalMarks);

        return TestResult::create([
            'test_attempt_id' => $attempt->id,
            'total_questions' => $totalQuestions,
            'attempted_questions' => $attemptedQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'unanswered_questions' => $unansweredQuestions,
            'total_marks' => $totalMarks,
            'obtained_marks' => $finalMarks,
            'negative_marks' => $negativeMarks,
            'percentage' => $percentage,
            'rank' => $rank,
            'percentile' => $percentile,
            'subject_wise_analysis' => $subjectWise,
            'difficulty_wise_analysis' => $difficultyWise,
            'time_wise_analysis' => $timeWise,
            'recommendations' => $this->generateRecommendations($subjectWise, $difficultyWise),
        ]);
    }

    /**
     * Generate AI-powered recommendations
     */
    private function generateRecommendations(array $subjectWise, array $difficultyWise): array
    {
        $recommendations = [];

        // Subject-wise recommendations
        foreach ($subjectWise as $subject => $stats) {
            $accuracy = $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0;
            if ($accuracy < 50) {
                $recommendations[] = "Focus more on {$subject} - your accuracy is " . round($accuracy, 1) . '%';
            }
        }

        // Difficulty-wise recommendations
        foreach ($difficultyWise as $difficulty => $stats) {
            $accuracy = $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0;
            if ($difficulty === 'hard' && $accuracy < 30) {
                $recommendations[] = "Work on difficult questions - your accuracy is " . round($accuracy, 1) . '%';
            }
        }

        return $recommendations;
    }

    /**
     * Get test attempt with full details
     */
    public function getTestAttemptWithDetails(int $attemptId): ?TestAttempt
    {
        return TestAttempt::with([
            'test',
            'answers.question.chapter.topic.subject',
            'answers.selectedOption',
            'result'
        ])->find($attemptId);
    }

    /**
     * Get user's test attempts
     */
    public function getUserTestAttempts(User $user, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return TestAttempt::with(['test', 'result'])
            ->forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Resume a test attempt
     */
    public function resumeTestAttempt(TestAttempt $attempt): TestAttempt
    {
        if (!$attempt->canResume()) {
            throw new \Exception('Test attempt cannot be resumed');
        }

        return $attempt;
    }

    /**
     * Update time remaining for an attempt
     */
    public function updateTimeRemaining(TestAttempt $attempt, int $seconds): void
    {
        $attempt->updateTimeRemaining($seconds);
    }

    /**
     * Bookmark a question in test
     */
    public function bookmarkQuestion(TestAttempt $attempt, int $questionId, ?string $notes = null): TestBookmark
    {
        return TestBookmark::updateOrCreate(
            [
                'user_id' => $attempt->user_id,
                'test_attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'notes' => $notes,
            ]
        );
    }

    /**
     * Get available tests
     */
    public function getAvailableTests(): Collection
    {
        return Test::available()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get test leaderboard
     */
    public function getTestLeaderboard(int $testId, int $limit = 50): Collection
    {
        return TestResult::with(['testAttempt.user'])
            ->whereHas('testAttempt', function ($query) use ($testId) {
                $query->where('test_id', $testId);
            })
            ->orderBy('percentage', 'desc')
            ->orderBy('obtained_marks', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's test statistics
     */
    public function getUserTestStatistics(User $user): array
    {
        $attempts = TestAttempt::forUser($user->id)->with('result')->get();

        $totalTests = $attempts->count();
        $completedTests = $attempts->where('status', 'completed')->count();
        $averageScore = $attempts->where('status', 'completed')->avg('result.percentage') ?? 0;
        $highestScore = $attempts->where('status', 'completed')->max('result.percentage') ?? 0;

        return [
            'total_tests' => $totalTests,
            'completed_tests' => $completedTests,
            'average_score' => round($averageScore, 2),
            'highest_score' => round($highestScore, 2),
            'completion_rate' => $totalTests > 0 ? round(($completedTests / $totalTests) * 100, 2) : 0,
        ];
    }
}