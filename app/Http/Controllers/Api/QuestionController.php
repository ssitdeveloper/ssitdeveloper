<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Bookmark;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionService $questionService
    ) {}

    /**
     * Get paginated questions with filters
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'chapter_id' => 'nullable|integer|exists:chapters,id',
            'difficulty' => ['nullable', Rule::enum(\App\Enums\DifficultyLevel::class)],
            'search' => 'nullable|string|min:2|max:255',
            'order_by' => 'nullable|string|in:created_at,views_count,attempts_count,random',
            'order_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $filters = $request->only([
            'subject_id', 'topic_id', 'chapter_id', 'difficulty', 'search',
            'order_by', 'order_direction'
        ]);

        $perPage = $request->get('per_page', 20);

        $questions = $this->questionService->getQuestions($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $questions,
            'meta' => [
                'filters' => $filters,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Get single question details
     */
    public function show(int $questionId): JsonResponse
    {
        $question = $this->questionService->getQuestionById($questionId);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found',
            ], 404);
        }

        // Record view
        $this->questionService->recordQuestionView($question);

        return response()->json([
            'success' => true,
            'data' => $question->load(['chapter.topic.subject', 'options']),
        ]);
    }

    /**
     * Get questions by chapter
     */
    public function getByChapter(int $chapterId, Request $request): JsonResponse
    {
        $request->validate([
            'difficulty' => ['nullable', Rule::enum(\App\Enums\DifficultyLevel::class)],
            'limit' => 'nullable|integer|min:1|max:100',
            'random' => 'nullable|boolean',
        ]);

        $filters = $request->only(['difficulty', 'limit', 'random']);

        $questions = $this->questionService->getQuestionsByChapter($chapterId, $filters);

        return response()->json([
            'success' => true,
            'data' => $questions,
            'meta' => [
                'chapter_id' => $chapterId,
                'filters' => $filters,
                'count' => $questions->count(),
            ],
        ]);
    }

    /**
     * Get random questions for practice
     */
    public function getRandom(Request $request): JsonResponse
    {
        $request->validate([
            'count' => 'nullable|integer|min:1|max:50',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'chapter_id' => 'nullable|integer|exists:chapters,id',
            'difficulty' => ['nullable', Rule::enum(\App\Enums\DifficultyLevel::class)],
        ]);

        $count = $request->get('count', 10);
        $filters = $request->only(['subject_id', 'topic_id', 'chapter_id', 'difficulty']);

        $questions = $this->questionService->getRandomQuestions($count, $filters);

        return response()->json([
            'success' => true,
            'data' => $questions,
            'meta' => [
                'count' => $count,
                'filters' => $filters,
                'actual_count' => $questions->count(),
            ],
        ]);
    }

    /**
     * Search questions
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'difficulty' => ['nullable', Rule::enum(\App\Enums\DifficultyLevel::class)],
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $query = $request->get('query');
        $filters = $request->only(['subject_id', 'difficulty']);
        $perPage = $request->get('per_page', 20);

        $results = $this->questionService->searchQuestions($query, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'query' => $query,
                'filters' => $filters,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Validate answer for a question
     */
    public function validateAnswer(Request $request, int $questionId): JsonResponse
    {
        $request->validate([
            'selected_option_id' => 'required|integer|exists:options,id',
        ]);

        $selectedOptionId = $request->get('selected_option_id');

        $result = $this->questionService->validateAnswer($questionId, $selectedOptionId);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Bookmark a question
     */
    public function bookmark(int $questionId): JsonResponse
    {
        $user = Auth::user();
        $question = Question::find($questionId);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found',
            ], 404);
        }

        $bookmarked = $this->questionService->bookmarkQuestion($user, $question);

        return response()->json([
            'success' => $bookmarked,
            'message' => $bookmarked ? 'Question bookmarked successfully' : 'Failed to bookmark question',
        ]);
    }

    /**
     * Remove bookmark from question
     */
    public function removeBookmark(int $questionId): JsonResponse
    {
        $user = Auth::user();
        $question = Question::find($questionId);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found',
            ], 404);
        }

        $removed = $this->questionService->removeBookmark($user, $question);

        return response()->json([
            'success' => $removed > 0,
            'message' => $removed > 0 ? 'Bookmark removed successfully' : 'Bookmark not found',
        ]);
    }

    /**
     * Get user's bookmarks
     */
    public function getBookmarks(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $user = Auth::user();
        $perPage = $request->get('per_page', 20);

        $bookmarks = $this->questionService->getUserBookmarks($user, $perPage);

        return response()->json([
            'success' => true,
            'data' => $bookmarks,
        ]);
    }

    /**
     * Get question statistics
     */
    public function getStats(int $questionId): JsonResponse
    {
        $stats = $this->questionService->getQuestionStats($questionId);

        if (empty($stats)) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get questions statistics with filters
     */
    public function getQuestionsStats(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'chapter_id' => 'nullable|integer|exists:chapters,id',
        ]);

        $filters = $request->only(['subject_id', 'topic_id', 'chapter_id']);

        $stats = $this->questionService->getQuestionsStats($filters);

        return response()->json([
            'success' => true,
            'data' => $stats,
            'meta' => [
                'filters' => $filters,
            ],
        ]);
    }
}
