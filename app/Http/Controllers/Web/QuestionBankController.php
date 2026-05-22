<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Chapter;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    public function __construct(
        private QuestionService $questionService
    ) {}

    /**
     * Display the question bank dashboard
     */
    public function index(Request $request): View
    {
        $subjects = Subject::with(['topics.chapters'])->get();

        $filters = $request->only([
            'subject_id', 'topic_id', 'chapter_id', 'difficulty', 'search'
        ]);

        $questions = $this->questionService->getQuestions($filters, 20);

        return view('question-bank.index', compact('subjects', 'questions', 'filters'));
    }

    /**
     * Display questions for a specific chapter
     */
    public function chapter(int $chapterId, Request $request): View
    {
        $chapter = Chapter::with(['topic.subject', 'questions'])->findOrFail($chapterId);

        $filters = $request->only(['difficulty', 'limit', 'random']);
        $questions = $this->questionService->getQuestionsByChapter($chapterId, $filters);

        return view('question-bank.chapter', compact('chapter', 'questions', 'filters'));
    }

    /**
     * Display single question for practice
     */
    public function show(int $questionId): View
    {
        $question = $this->questionService->getQuestionById($questionId);

        if (!$question) {
            abort(404, 'Question not found');
        }

        // Record view
        $this->questionService->recordQuestionView($question);

        $user = Auth::user();
        $isBookmarked = $user ? $user->bookmarks()->where('question_id', $questionId)->exists() : false;

        return view('question-bank.show', compact('question', 'isBookmarked'));
    }

    /**
     * Practice mode - random questions
     */
    public function practice(Request $request): View
    {
        $request->validate([
            'count' => 'nullable|integer|min:1|max:50',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'chapter_id' => 'nullable|integer|exists:chapters,id',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
        ]);

        $count = $request->get('count', 10);
        $filters = $request->only(['subject_id', 'topic_id', 'chapter_id', 'difficulty']);

        $questions = $this->questionService->getRandomQuestions($count, $filters);

        return view('question-bank.practice', compact('questions', 'filters', 'count'));
    }

    /**
     * Search questions
     */
    public function search(Request $request): View
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
        ]);

        $query = $request->get('query');
        $filters = $request->only(['subject_id', 'difficulty']);
        $results = $this->questionService->searchQuestions($query, $filters, 20);

        return view('question-bank.search', compact('results', 'query', 'filters'));
    }

    /**
     * Validate answer via AJAX
     */
    public function validateAnswer(Request $request, int $questionId): RedirectResponse
    {
        $request->validate([
            'selected_option_id' => 'required|integer|exists:options,id',
        ]);

        $selectedOptionId = $request->get('selected_option_id');
        $result = $this->questionService->validateAnswer($questionId, $selectedOptionId);

        return back()->with([
            'validation_result' => $result,
            'question_id' => $questionId,
        ]);
    }

    /**
     * Toggle bookmark for a question
     */
    public function toggleBookmark(int $questionId): RedirectResponse
    {
        $user = Auth::user();
        $question = $this->questionService->getQuestionById($questionId);

        if (!$question) {
            return back()->with('error', 'Question not found');
        }

        $isBookmarked = $user->bookmarks()->where('question_id', $questionId)->exists();

        if ($isBookmarked) {
            $this->questionService->removeBookmark($user, $question);
            $message = 'Bookmark removed successfully';
        } else {
            $this->questionService->bookmarkQuestion($user, $question);
            $message = 'Question bookmarked successfully';
        }

        return back()->with('success', $message);
    }

    /**
     * Display user's bookmarks
     */
    public function bookmarks(Request $request): View
    {
        $user = Auth::user();
        $bookmarks = $this->questionService->getUserBookmarks($user, 20);

        return view('question-bank.bookmarks', compact('bookmarks'));
    }

    /**
     * Get subjects/topics/chapters via AJAX for filters
     */
    public function getSubjects(): \Illuminate\Http\JsonResponse
    {
        $subjects = Subject::with(['topics.chapters'])->get();

        return response()->json([
            'subjects' => $subjects,
        ]);
    }

    public function getTopics(int $subjectId): \Illuminate\Http\JsonResponse
    {
        $topics = Topic::where('subject_id', $subjectId)->with('chapters')->get();

        return response()->json([
            'topics' => $topics,
        ]);
    }

    public function getChapters(int $topicId): \Illuminate\Http\JsonResponse
    {
        $chapters = Chapter::where('topic_id', $topicId)->get();

        return response()->json([
            'chapters' => $chapters,
        ]);
    }
}