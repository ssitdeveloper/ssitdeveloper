<?php

namespace App\Services;

use App\Models\LearningSession;
use App\Models\Question;
use App\Models\Topic;
use App\Models\Subject;
use App\Models\LearningQuestionAttempt;
use App\Models\SessionQuestionHistory;
use App\Models\UserWeakTopic;
use App\Models\LearningRecommendation;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LearningSessionService
{
    /**
     * Start a new learning session
     */
    public function startSession(User $user, array $params): LearningSession
    {
        $subject = Subject::findOrFail($params['subject_id']);

        // Get questions based on criteria
        $questions = $this->getSessionQuestions(
            $params['subject_id'],
            $params['topic_id'] ?? null,
            $params['difficulty_level'] ?? null,
            $params['question_count'] ?? 10
        );

        // Create session
        $session = LearningSession::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'topic_id' => $params['topic_id'] ?? null,
            'mode' => $params['session_type'] ?? 'practice',
            'total_questions' => $questions->count(),
            'questions_completed' => 0,
            'correct_answers' => 0,
            'current_question_index' => 0,
            'status' => 'active',
            'started_at' => now(),
            'session_data' => [
                'session_type' => $params['session_type'] ?? 'practice',
                'difficulty_level' => $params['difficulty_level'] ?? 'mixed',
            ]
        ]);

        // Create question history
        $questions->each(function ($question, $index) use ($session) {
            SessionQuestionHistory::create([
                'session_id' => $session->id,
                'question_id' => $question->id,
                'display_order' => $index + 1,
                'visit_status' => 'not_visited'
            ]);
        });

        return $session;
    }

    /**
     * Get questions for a session
     */
    private function getSessionQuestions(
        int $subjectId,
        ?int $topicId,
        ?string $difficulty,
        int $count
    ): Collection {
        $query = Question::where('subject_id', $subjectId)
            ->where('is_active', true);

        if ($topicId) {
            $query->where('topic_id', $topicId);
        }

        if ($difficulty && $difficulty !== 'mixed') {
            $query->where('difficulty_level', $difficulty);
        }

        return $query->inRandomOrder()->limit($count)->get();
    }

    /**
     * Get the next question in session
     */
    public function getNextQuestion(LearningSession $session): ?array
    {
        // Get next unvisited question
        $nextQuestion = $session->questionHistory()
            ->where('visit_status', 'not_visited')
            ->first();

        if (!$nextQuestion) {
            return null;
        }

        // Update visit status
        $nextQuestion->update(['visit_status' => 'visited']);

        $question = $nextQuestion->question->load('options');
        $options = $question->options->map(function ($option) {
            return [
                'id' => $option->id,
                'text' => $option->option_text,
                'image' => $option->option_image,
                'order' => $option->order_by ?? 1
            ];
        })->sortBy('order')->values();

        return [
            'id' => $question->id,
            'question_text' => $question->question_text,
            'question_image' => $question->question_image,
            'question_type' => $question->question_type,
            'marks' => $question->marks,
            'negative_marks' => $question->negative_marks,
            'hint' => $question->hint,
            'question_number' => $session->questions_completed + 1,
            'total_questions' => $session->total_questions,
            'options' => $options->toArray(),
        ];
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
     * Get last active session to resume
     */
    public function getLastActiveSession(User $user): ?LearningSession
    {
        return LearningSession::where('user_id', $user->id)
            ->where('status', 'paused')
            ->where('questions_completed', '<', $this->raw('total_questions'))
            ->latest()
            ->first();
    }

    /**
     * Complete a session
     */
    public function completeSession(LearningSession $session): void
    {
        $session->complete();

        // Trigger weak topic detection
        app(WeakTopicDetectionService::class)->detectWeakTopics($session->user_id);

        // Generate recommendations
        app(RecommendationEngine::class)->generateRecommendations($session->user_id);
    }

    /**
     * Pause session
     */
    public function pauseSession(LearningSession $session): void
    {
        $session->pause();
    }

    /**
     * Get session summary
     */
    public function getSessionSummary(LearningSession $session): array
    {
        return $session->getSummary();
    }

    /**
     * Get session statistics
     */
    public function getSessionStats(LearningSession $session): array
    {
        $totalTime = $session->getElapsedSeconds();
        $avgTimePerQuestion = $session->questions_completed > 0
            ? round($totalTime / $session->questions_completed, 2)
            : 0;

        return [
            'total_questions' => $session->total_questions,
            'completed' => $session->questions_completed,
            'correct' => $session->correct_answers,
            'wrong' => $session->questions_completed - $session->correct_answers,
            'skipped' => $session->total_questions - $session->questions_completed,
            'accuracy' => $session->getAccuracyPercentage(),
            'progress' => $session->getProgressPercentage(),
            'total_time_seconds' => $totalTime,
            'avg_time_per_question' => $avgTimePerQuestion,
        ];
    }
}
