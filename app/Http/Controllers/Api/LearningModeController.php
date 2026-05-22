<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{
    LearningSession, Question, Subject, Topic, Chapter, LearningExplanation
};
use App\Services\LearningModeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LearningModeController extends Controller
{
    public function __construct(
        private LearningModeService $learningModeService
    ) {}

    /**
     * Start a learning session
     */
    public function startSession(Request $request): JsonResponse
    {
        $request->validate([
            'mode' => 'required|in:subject,topic,chapter,custom',
            'subject_id' => 'nullable|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'chapter_id' => 'nullable|exists:chapters,id'
        ]);

        try {
            $user = Auth::user();
            $subject = $request->subject_id ? Subject::find($request->subject_id) : null;
            $topic = $request->topic_id ? Topic::find($request->topic_id) : null;
            $chapter = $request->chapter_id ? Chapter::find($request->chapter_id) : null;

            $session = $this->learningModeService->startLearningSession(
                $user,
                $request->mode,
                $subject,
                $topic,
                $chapter
            );

            $nextQuestion = $this->learningModeService->getNextQuestion($session);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => $session,
                    'next_question' => $nextQuestion
                ],
                'message' => 'Learning session started successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get current session state
     */
    public function getSession(LearningSession $session): JsonResponse
    {
        $this->authorize('view', $session);

        return response()->json([
            'success' => true,
            'data' => [
                'session' => $session,
                'progress' => [
                    'completed' => $session->questions_completed,
                    'total' => $session->total_questions,
                    'percentage' => $session->getProgressPercentage(),
                    'accuracy' => $session->getAccuracyPercentage()
                ],
                'next_question' => $this->learningModeService->getNextQuestion($session)
            ]
        ]);
    }

    /**
     * Get a specific question
     */
    public function getQuestion(LearningSession $session, Question $question): JsonResponse
    {
        $this->authorize('view', $session);

        // Check if question is part of this session
        if (!in_array($question->id, $session->session_data['question_ids'] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Question not part of this session'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'question' => $question->load('options'),
                'question_index' => array_search($question->id, $session->session_data['question_ids'] ?? []) + 1,
                'total_questions' => $session->total_questions
            ]
        ]);
    }

    /**
     * Submit an answer
     */
    public function submitAnswer(LearningSession $session, Request $request): JsonResponse
    {
        $this->authorize('update', $session);

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:options,id',
            'time_spent_seconds' => 'required|integer|min:0'
        ]);

        try {
            $question = Question::find($request->question_id);

            if (!in_array($question->id, $session->session_data['question_ids'] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not part of this session'
                ], 403);
            }

            $attempt = $this->learningModeService->submitAnswer(
                $session,
                $question,
                $request->option_id,
                $request->time_spent_seconds
            );

            $explanation = $this->learningModeService->getExplanation($question);

            return response()->json([
                'success' => true,
                'data' => [
                    'attempt' => $attempt,
                    'is_correct' => $attempt->is_correct,
                    'correct_option_id' => $attempt->getCorrectOption()?->id,
                    'explanation' => $explanation,
                    'session_progress' => [
                        'completed' => $session->questions_completed,
                        'total' => $session->total_questions,
                        'accuracy' => $session->getAccuracyPercentage()
                    ]
                ],
                'message' => $attempt->is_correct ? 'Correct!' : 'Incorrect. Check the explanation.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get explanation for a question
     */
    public function getExplanation(Question $question): JsonResponse
    {
        $explanation = $this->learningModeService->getExplanation($question);

        if (!$explanation) {
            return response()->json([
                'success' => false,
                'message' => 'Explanation not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $explanation
        ]);
    }

    /**
     * Rate explanation
     */
    public function rateExplanation(Question $question, Request $request): JsonResponse
    {
        $request->validate([
            'is_helpful' => 'required|boolean'
        ]);

        $explanation = $question->explanation;

        if (!$explanation) {
            return response()->json([
                'success' => false,
                'message' => 'Explanation not found'
            ], 404);
        }

        $this->learningModeService->rateExplanation($explanation, $request->is_helpful);

        return response()->json([
            'success' => true,
            'message' => 'Rating recorded successfully'
        ]);
    }

    /**
     * Get session summary
     */
    public function getSessionSummary(LearningSession $session): JsonResponse
    {
        $this->authorize('view', $session);

        $summary = $this->learningModeService->getSessionSummary($session);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Resume a paused session
     */
    public function resumeSession(LearningSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        try {
            $session = $this->learningModeService->resumeSession($session);
            $nextQuestion = $this->learningModeService->getNextQuestion($session);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => $session,
                    'next_question' => $nextQuestion
                ],
                'message' => 'Session resumed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Complete a session
     */
    public function completeSession(LearningSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        try {
            $this->learningModeService->completeSession($session);
            $summary = $this->learningModeService->getSessionSummary($session);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => $session,
                    'summary' => $summary
                ],
                'message' => 'Session completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): JsonResponse
    {
        $user = Auth::user();
        $statistics = $this->learningModeService->getUserStatistics($user);

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Get weak topics
     */
    public function getWeakTopics(): JsonResponse
    {
        $user = Auth::user();
        $weakTopics = $this->learningModeService->getUserWeakTopics($user);

        return response()->json([
            'success' => true,
            'data' => $weakTopics
        ]);
    }

    /**
     * Get adaptive recommendations
     */
    public function getRecommendations(): JsonResponse
    {
        $user = Auth::user();
        $recommendations = $this->learningModeService->getAdaptiveRecommendations($user);

        return response()->json([
            'success' => true,
            'data' => $recommendations
        ]);
    }

    /**
     * Bookmark a question
     */
    public function bookmarkQuestion(LearningSession $session, Request $request): JsonResponse
    {
        $this->authorize('update', $session);

        $request->validate([
            'question_id' => 'required|exists:questions,id'
        ]);

        try {
            $question = Question::find($request->question_id);
            $this->learningModeService->bookmarkQuestion($session, $question);

            return response()->json([
                'success' => true,
                'message' => 'Question bookmarked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Pause session
     */
    public function pauseSession(LearningSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $session->pause();

        return response()->json([
            'success' => true,
            'data' => $session,
            'message' => 'Session paused successfully'
        ]);
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions(): JsonResponse
    {
        $user = Auth::user();
        $sessions = LearningSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'paused'])
            ->with(['subject', 'topic', 'chapter'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }
}
