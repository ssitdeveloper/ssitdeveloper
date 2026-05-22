<?php

namespace App\Services;

use App\Enums\TestStatus;
use App\Models\Option;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Models\TestResult;
use App\Models\User;

class TestService
{
    public function startTest(User $user, Test $test): TestAttempt
    {
        \Log::debug('TestService::startTest', [
            'user_id' => $user->id,
            'test_id' => $test->id,
            'test_slug' => $test->slug,
        ]);

        // Get randomized questions for this test
        $questions = $test->getQuestionsRandomized();
        $questionIds = $questions->pluck('id')->toArray();

        return TestAttempt::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'started_at' => now(),
            'status' => TestStatus::IN_PROGRESS,
            'question_ids' => $questionIds,
            'expires_at' => now()->addMinutes($test->duration_minutes),
        ]);
    }

    public function submitAnswer(TestAttempt $attempt, int $questionId, ?int $optionId, int $timeSpent): TestAttemptAnswer
    {
        // Get the correct option for this question
        $question = Question::findOrFail($questionId);
        $correctOption = $question->options()->where('is_correct', true)->first();

        // Determine if the answer is correct
        $isCorrect = false;
        if ($optionId && $correctOption && $correctOption->id === $optionId) {
            $isCorrect = true;
        }

        // Create or update the answer record
        $answer = TestAttemptAnswer::updateOrCreate(
            [
                'test_attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $optionId,
                'answered_at' => now(),
            ]
        );

        return $answer;
    }

    public function completeTest(TestAttempt $attempt): TestResult
    {
        // Update attempt status
        $attempt->update([
            'status' => TestStatus::COMPLETED,
            'submitted_at' => now(),
        ]);

        // Calculate results
        $answers = $attempt->answers;
        $totalQuestions = count($attempt->question_ids);

        $correctAnswers = 0;
        $totalMarks = 0;

        foreach ($answers as $answer) {
            $question = Question::findOrFail($answer->question_id);
            $correctOption = $question->options()->where('is_correct', true)->first();

            if ($answer->selected_option_id === $correctOption?->id) {
                $correctAnswers++;
                $totalMarks += $attempt->test->marks_per_question;
            } else {
                $totalMarks -= $attempt->test->negative_marking;
            }
        }

        $obtainedMarks = max(0, $totalMarks);
        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // Create test result
        $result = TestResult::create([
            'test_attempt_id' => $attempt->id,
            'total_questions' => $totalQuestions,
            'attempted_questions' => $answers->count(),
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $answers->count() - $correctAnswers,
            'unanswered_questions' => $totalQuestions - $answers->count(),
            'total_marks' => $attempt->test->total_questions * $attempt->test->marks_per_question,
            'obtained_marks' => $obtainedMarks,
            'negative_marks' => abs(min(0, $totalMarks - $correctAnswers * $attempt->test->marks_per_question)),
            'percentage' => $percentage,
        ]);

        return $result;
    }

    public function getResults(TestAttempt $attempt): array
    {
        $result = $attempt->result;

        if (!$result) {
            $result = $this->completeTest($attempt);
        }

        return [
            'test_name' => $attempt->test->title,
            'total_questions' => $result->total_questions,
            'attempted' => $result->attempted_questions,
            'correct' => $result->correct_answers,
            'wrong' => $result->wrong_answers,
            'unanswered' => $result->unanswered_questions,
            'obtained_marks' => $result->obtained_marks,
            'total_marks' => $result->total_marks,
            'percentage' => round($result->percentage, 2),
            'status' => $attempt->status,
        ];
    }

    public function getUserTestHistory(User $user, int $limit = 10)
    {
        return $user->testAttempts()
            ->where('status', TestStatus::COMPLETED)
            ->with('test', 'result')
            ->latest()
            ->paginate($limit);
    }

    public function getTestLeaderboard(Test $test, int $limit = 10)
    {
        return TestAttempt::where('test_id', $test->id)
            ->where('status', TestStatus::COMPLETED)
            ->with('user', 'result')
            ->orderBy('percentage', 'desc')
            ->limit($limit)
            ->get();
    }
}
