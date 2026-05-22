<?php

namespace App\Services;

use App\Models\{
    User, Subject, Topic, Chapter, Question, LearningSession,
    LearningQuestionAttempt, LearningExplanation, UserLearningPreference,
    UserWeakTopic, LearningRecommendation, LearningProgress,
    SessionQuestionHistory, LearningHintUsed
};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LearningModeService
{
    /**
     * Start a new learning session
     */
    public function startLearningSession(
        User $user,
        string $mode,
        ?Subject $subject = null,
        ?Topic $topic = null,
        ?Chapter $chapter = null
    ): LearningSession {
        return DB::transaction(function () use ($user, $mode, $subject, $topic, $chapter) {
            // Get questions based on mode
            $questions = $this->getQuestionsForMode($mode, $subject, $topic, $chapter);
            $questionIds = $questions->pluck('id')->toArray();

            if (empty($questionIds)) {
                throw new \Exception('No questions available for this selection');
            }

            // Create learning session
            $session = LearningSession::create([
                'user_id' => $user->id,
                'subject_id' => $subject?->id,
                'topic_id' => $topic?->id,
                'chapter_id' => $chapter?->id,
                'mode' => $mode,
                'total_questions' => count($questionIds),
                'status' => 'active',
                'started_at' => now(),
                'session_data' => [
                    'question_ids' => $questionIds,
                    'shuffle_questions' => UserLearningPreference::getOrCreateForUser($user)->shuffle_questions
                ]
            ]);

            // Create question history
            foreach ($questionIds as $index => $questionId) {
                SessionQuestionHistory::create([
                    'learning_session_id' => $session->id,
                    'question_id' => $questionId,
                    'question_order' => $index + 1,
                    'visit_status' => 'not_visited'
                ]);
            }

            return $session;
        });
    }

    /**
     * Get questions based on learning mode
     */
    private function getQuestionsForMode(
        string $mode,
        ?Subject $subject = null,
        ?Topic $topic = null,
        ?Chapter $chapter = null
    ): Collection {
        $preferences = UserLearningPreference::where('user_id', auth()->id())->first();
        $limit = $preferences?->questions_per_session ?? 10;

        $query = Question::published();

        if ($mode === 'subject' && $subject) {
            $query = $query->whereHas('chapter.topic.subject', function ($q) use ($subject) {
                $q->where('id', $subject->id);
            });
        } elseif ($mode === 'topic' && $topic) {
            $query = $query->whereHas('chapter.topic', function ($q) use ($topic) {
                $q->where('id', $topic->id);
            });
        } elseif ($mode === 'chapter' && $chapter) {
            $query = $query->where('chapter_id', $chapter->id);
        }

        return $query->inRandomOrder()->limit($limit)->get();
    }

    /**
     * Get next question in session
     */
    public function getNextQuestion(LearningSession $session)
    {
        $nextHistory = $session->questionHistory()
            ->where('visit_status', 'not_visited')
            ->orderBy('question_order')
            ->first();

        if (!$nextHistory) {
            return null;
        }

        return $nextHistory->question;
    }

    /**
     * Submit answer and check if correct
     */
    public function submitAnswer(
        LearningSession $session,
        Question $question,
        ?int $optionId = null,
        int $timeSpentSeconds = 0
    ): LearningQuestionAttempt {
        return DB::transaction(function () use ($session, $question, $optionId, $timeSpentSeconds) {
            // Check if answer is correct
            $isCorrect = false;
            if ($optionId) {
                $selectedOption = $question->options()->find($optionId);
                $isCorrect = $selectedOption && $selectedOption->is_correct;
            }

            // Create attempt record
            $attempt = LearningQuestionAttempt::create([
                'learning_session_id' => $session->id,
                'user_id' => $session->user_id,
                'question_id' => $question->id,
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'time_spent_seconds' => $timeSpentSeconds,
                'attempt_number' => 1
            ]);

            // Update session progress
            $session->increment('questions_completed');
            if ($isCorrect) {
                $session->increment('correct_answers');
            }

            // Update question history
            $session->questionHistory()
                ->where('question_id', $question->id)
                ->first()
                ?->markAsAnswered();

            // Update weak topic tracking
            $this->updateWeakTopicTracking($session->user_id, $question, $isCorrect);

            return $attempt;
        });
    }

    /**
     * Get explanation for a question
     */
    public function getExplanation(Question $question): ?LearningExplanation
    {
        $explanation = $question->explanation;

        if ($explanation) {
            $explanation->incrementViews();
        }

        return $explanation;
    }

    /**
     * Get current session questions with answers
     */
    public function getSessionSummary(LearningSession $session): array
    {
        $attempts = $session->attempts()
            ->with(['question', 'selectedOption'])
            ->get();

        $summary = [
            'total' => $session->total_questions,
            'completed' => $session->questions_completed,
            'correct' => $session->correct_answers,
            'wrong' => $session->questions_completed - $session->correct_answers,
            'accuracy' => $session->getAccuracyPercentage(),
            'progress' => $session->getProgressPercentage(),
            'time_elapsed' => $session->getElapsedSeconds(),
            'questions' => []
        ];

        foreach ($attempts as $attempt) {
            $summary['questions'][] = [
                'question_id' => $attempt->question_id,
                'question_text' => $attempt->question->question_text,
                'your_answer' => $attempt->getAnswer(),
                'correct_answer' => $attempt->getCorrectAnswer(),
                'is_correct' => $attempt->is_correct,
                'status' => $attempt->getStatus(),
                'time_spent' => $attempt->getTimeSpentFormatted()
            ];
        }

        return $summary;
    }

    /**
     * Update weak topic tracking
     */
    private function updateWeakTopicTracking(int $userId, Question $question, bool $isCorrect): void
    {
        $topic = $question->chapter->topic;

        $weakTopic = UserWeakTopic::firstOrCreate([
            'user_id' => $userId,
            'subject_id' => $topic->subject_id,
            'topic_id' => $topic->id
        ]);

        $weakTopic->addAttempt($isCorrect);
        $weakTopic->recordTrend($weakTopic->accuracy_percentage);
    }

    /**
     * Generate weak topic recommendations
     */
    public function generateRecommendations(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Get weak topics
            $weakTopics = UserWeakTopic::where('user_id', $user->id)
                ->where('status', 'weak')
                ->where('total_attempts', '>=', 5) // At least 5 attempts
                ->orderBy('accuracy_percentage')
                ->get();

            foreach ($weakTopics as $weakTopic) {
                // Check if recommendation already exists and is active
                $existing = LearningRecommendation::where('user_id', $user->id)
                    ->where('topic_id', $weakTopic->topic_id)
                    ->where('is_active', true)
                    ->first();

                if ($existing) {
                    continue;
                }

                // Get questions for this topic
                $questions = Question::whereHas('chapter.topic', function ($q) use ($weakTopic) {
                    $q->where('id', $weakTopic->topic_id);
                })->published()->limit(10)->get();

                // Create recommendation
                LearningRecommendation::create([
                    'user_id' => $user->id,
                    'subject_id' => $weakTopic->subject_id,
                    'topic_id' => $weakTopic->topic_id,
                    'recommendation_type' => 'weak_topic',
                    'recommendation_text' => "You're struggling with {$weakTopic->topic->name}. "
                        . "Your accuracy is {$weakTopic->accuracy_percentage}%. "
                        . "We recommend revising this topic with focused practice.",
                    'priority' => $this->calculatePriority($weakTopic),
                    'target_question_ids' => $questions->pluck('id')->toArray(),
                    'estimated_time_minutes' => count($questions) * 3,
                    'is_active' => true
                ]);
            }
        });
    }

    /**
     * Calculate recommendation priority
     */
    private function calculatePriority(UserWeakTopic $weakTopic): int
    {
        $priority = 0;

        // Lower accuracy = higher priority
        if ($weakTopic->accuracy_percentage < 30) {
            $priority += 40;
        } elseif ($weakTopic->accuracy_percentage < 50) {
            $priority += 30;
        }

        // Recent attempts = higher priority
        if ($weakTopic->last_attempted_at?->diffInDays() <= 2) {
            $priority += 20;
        }

        // Declining trend = higher priority
        if ($weakTopic->getTrendStatus() === 'declining') {
            $priority += 20;
        }

        return min($priority, 100);
    }

    /**
     * Get adaptive recommendations
     */
    public function getAdaptiveRecommendations(User $user): Collection
    {
        return LearningRecommendation::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('is_accepted', '!=', false)
            ->orderBy('priority', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Resume a paused session
     */
    public function resumeSession(LearningSession $session): LearningSession
    {
        if (!$session->canResume()) {
            throw new \Exception('This session cannot be resumed');
        }

        $session->resume();
        return $session;
    }

    /**
     * Complete a learning session
     */
    public function completeSession(LearningSession $session): void
    {
        DB::transaction(function () use ($session) {
            $session->complete();

            // Update learning progress
            if ($session->subject_id) {
                $progress = LearningProgress::getOrCreateForSubject(
                    $session->user,
                    $session->subject
                );
                $progress->updateFromSession($session);
            }

            // Generate recommendations if needed
            $this->generateRecommendations($session->user);
        });
    }

    /**
     * Mark explanation as helpful/unhelpful
     */
    public function rateExplanation(LearningExplanation $explanation, bool $isHelpful): void
    {
        if ($isHelpful) {
            $explanation->markAsHelpful();
        } else {
            $explanation->markAsUnhelpful();
        }
    }

    /**
     * Bookmark a question
     */
    public function bookmarkQuestion(LearningSession $session, Question $question): void
    {
        $session->attempts()
            ->where('question_id', $question->id)
            ->first()
            ?->toggleBookmark();
    }

    /**
     * Get user's learning statistics
     */
    public function getUserStatistics(User $user): array
    {
        $sessions = LearningSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $totalQuestions = $sessions->sum('questions_completed');
        $totalCorrect = $sessions->sum('correct_answers');
        $totalTime = $sessions->sum(function ($session) {
            return intdiv($session->getElapsedSeconds(), 60);
        });

        return [
            'total_sessions' => $sessions->count(),
            'total_questions' => $totalQuestions,
            'total_correct' => $totalCorrect,
            'overall_accuracy' => $totalQuestions > 0
                ? round(($totalCorrect / $totalQuestions) * 100, 2)
                : 0,
            'total_time_hours' => round($totalTime / 60, 2),
            'avg_accuracy_per_session' => round(
                $sessions->avg(function ($s) { return $s->getAccuracyPercentage(); }),
                2
            )
        ];
    }

    /**
     * Get weak topics for user
     */
    public function getUserWeakTopics(User $user): Collection
    {
        return UserWeakTopic::where('user_id', $user->id)
            ->where('status', 'weak')
            ->with(['subject', 'topic'])
            ->orderBy('accuracy_percentage')
            ->get();
    }
}
