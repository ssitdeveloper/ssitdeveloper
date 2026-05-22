<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Services\MockTestService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TestController extends Controller
{
    public function __construct(
        private MockTestService $mockTestService
    ) {}

    /**
     * Get available tests
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $perPage = $request->get('per_page', 20);
        $tests = $this->mockTestService->getAvailableTests();

        return response()->json([
            'success' => true,
            'data' => $tests,
            'meta' => [
                'count' => $tests->count(),
            ],
        ]);
    }

    /**
     * Get test details
     */
    public function show(Test $test): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $test->load('attempts'),
        ]);
    }

    /**
     * Start a test attempt
     */
    public function startTest(Test $test): JsonResponse
    {
        try {
            $user = Auth::user();
            $attempt = $this->mockTestService->startTestAttempt($user, $test);

            return response()->json([
                'success' => true,
                'data' => $attempt->load('test'),
                'message' => 'Test started successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get test attempt details
     */
    public function getAttempt(TestAttempt $attempt): JsonResponse
    {
        // Check if user owns this attempt
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $attempt = $this->mockTestService->getTestAttemptWithDetails($attempt->id);

        return response()->json([
            'success' => true,
            'data' => $attempt,
        ]);
    }

    /**
     * Get current question
     */
    public function getQuestion(TestAttempt $attempt, Request $request): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'question_id' => 'required|integer|in:' . implode(',', $attempt->question_ids),
        ]);

        $questionId = $request->get('question_id');
        $question = \App\Models\Question::with(['options', 'chapter.topic.subject'])->find($questionId);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found',
            ], 404);
        }

        // Get user's answer for this question
        $answer = $attempt->answers()->where('question_id', $questionId)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'question' => $question,
                'user_answer' => $answer,
                'question_index' => $attempt->getCurrentQuestionIndex($questionId),
                'total_questions' => count($attempt->question_ids),
                'time_remaining' => $attempt->getTimeRemainingSeconds(),
            ],
        ]);
    }

    /**
     * Save answer
     */
    public function saveAnswer(TestAttempt $attempt, Request $request): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'question_id' => 'required|integer|in:' . implode(',', $attempt->question_ids),
            'selected_option_id' => 'nullable|integer|exists:options,id',
            'mark_for_review' => 'boolean',
        ]);

        try {
            $answer = $this->mockTestService->saveAnswer(
                $attempt,
                $request->question_id,
                $request->selected_option_id,
                $request->boolean('mark_for_review', false)
            );

            return response()->json([
                'success' => true,
                'data' => $answer,
                'message' => 'Answer saved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update time remaining
     */
    public function updateTime(TestAttempt $attempt, Request $request): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'time_remaining_seconds' => 'required|integer|min:0',
        ]);

        $this->mockTestService->updateTimeRemaining($attempt, $request->time_remaining_seconds);

        return response()->json([
            'success' => true,
            'message' => 'Time updated successfully',
        ]);
    }

    /**
     * Submit test
     */
    public function submitTest(TestAttempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $result = $this->mockTestService->submitTest($attempt);

            return response()->json([
                'success' => true,
                'data' => $result->load('testAttempt.test'),
                'message' => 'Test submitted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get test result
     */
    public function getResult(TestAttempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!$attempt->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Test not completed yet',
            ], 400);
        }

        $result = $attempt->result;

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Result not available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result->load('testAttempt.test'),
        ]);
    }

    /**
     * Get user's test attempts
     */
    public function getUserAttempts(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $user = Auth::user();
        $perPage = $request->get('per_page', 20);

        $attempts = $this->mockTestService->getUserTestAttempts($user, $perPage);

        return response()->json([
            'success' => true,
            'data' => $attempts,
        ]);
    }

    /**
     * Get test leaderboard
     */
    public function getLeaderboard(Test $test, Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'nullable|integer|min:10|max:100',
        ]);

        $limit = $request->get('limit', 50);
        $leaderboard = $this->mockTestService->getTestLeaderboard($test->id, $limit);

        return response()->json([
            'success' => true,
            'data' => $leaderboard,
        ]);
    }

    /**
     * Get user's test statistics
     */
    public function getUserStatistics(): JsonResponse
    {
        $user = Auth::user();
        $statistics = $this->mockTestService->getUserTestStatistics($user);

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Bookmark question
     */
    public function bookmarkQuestion(TestAttempt $attempt, Request $request): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $bookmark = $this->mockTestService->bookmarkQuestion(
                $attempt,
                $request->question_id,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'data' => $bookmark,
                'message' => 'Question bookmarked successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Resume test attempt
     */
    public function resumeTest(TestAttempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $attempt = $this->mockTestService->resumeTestAttempt($attempt);

            return response()->json([
                'success' => true,
                'data' => $attempt->load('test'),
                'message' => 'Test resumed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
