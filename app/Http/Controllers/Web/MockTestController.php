<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Services\MockTestService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MockTestController extends Controller
{
    public function __construct(
        private MockTestService $mockTestService
    ) {}

    /**
     * Show available tests
     */
    public function index(): View
    {
        $tests = $this->mockTestService->getAvailableTests();

        return view('mock-tests.index', compact('tests'));
    }

    /**
     * Show test details
     */
    public function show(Test $test): View
    {
        $user = Auth::user();
        $previousAttempts = $user->testAttempts()
            ->where('test_id', $test->id)
            ->with('result')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mock-tests.show', compact('test', 'previousAttempts'));
    }

    /**
     * Start a test attempt
     */
    public function start(Test $test): RedirectResponse
    {
        try {
            $user = Auth::user();
            $attempt = $this->mockTestService->startTestAttempt($user, $test);

            return redirect()->route('mock-tests.take', $attempt->id)
                ->with('success', 'Test started successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Take the test (main test interface)
     */
    public function take(TestAttempt $attempt): View
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$attempt->isInProgress()) {
            return redirect()->route('mock-tests.result', $attempt->id)
                ->with('info', 'Test has already been completed.');
        }

        $attempt = $this->mockTestService->getTestAttemptWithDetails($attempt->id);
        $currentQuestionId = $attempt->question_ids[0] ?? null;

        if (!$currentQuestionId) {
            return redirect()->route('mock-tests.show', $attempt->test_id)
                ->with('error', 'No questions available for this test.');
        }

        $question = \App\Models\Question::with(['options', 'chapter.topic.subject'])
            ->find($currentQuestionId);
        $userAnswer = $attempt->answers()->where('question_id', $currentQuestionId)->first();

        // Get question navigation data
        $questionNavigation = $this->getQuestionNavigation($attempt);

        return view('mock-tests.take', compact(
            'attempt',
            'question',
            'userAnswer',
            'questionNavigation'
        ));
    }

    /**
     * Get question for AJAX requests
     */
    public function getQuestion(TestAttempt $attempt, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'question_id' => 'required|integer|in:' . implode(',', $attempt->question_ids),
        ]);

        $questionId = $request->get('question_id');
        $question = \App\Models\Question::with(['options', 'chapter.topic.subject'])
            ->find($questionId);

        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        $userAnswer = $attempt->answers()->where('question_id', $questionId)->first();
        $questionIndex = $attempt->getCurrentQuestionIndex($questionId);

        return response()->json([
            'question' => $question,
            'user_answer' => $userAnswer,
            'question_index' => $questionIndex,
            'total_questions' => count($attempt->question_ids),
            'time_remaining' => $attempt->getTimeRemainingSeconds(),
        ]);
    }

    /**
     * Save answer via AJAX
     */
    public function saveAnswer(TestAttempt $attempt, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
                'message' => 'Answer saved successfully',
                'answer' => $answer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update time remaining
     */
    public function updateTime(TestAttempt $attempt, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'time_remaining_seconds' => 'required|integer|min:0',
        ]);

        $this->mockTestService->updateTimeRemaining($attempt, $request->time_remaining_seconds);

        return response()->json(['success' => true]);
    }

    /**
     * Submit test
     */
    public function submit(TestAttempt $attempt): RedirectResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $result = $this->mockTestService->submitTest($attempt);

            return redirect()->route('mock-tests.result', $attempt->id)
                ->with('success', 'Test submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show test result
     */
    public function result(TestAttempt $attempt): View
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$attempt->isCompleted()) {
            return redirect()->route('mock-tests.take', $attempt->id)
                ->with('info', 'Please complete the test first.');
        }

        $result = $attempt->result;

        if (!$result) {
            abort(404, 'Result not found');
        }

        $result = $result->load('testAttempt.test');
        $detailedAnswers = $attempt->answers()->with(['question.options', 'selectedOption'])->get();

        // Calculate detailed statistics
        $statistics = $this->calculateDetailedStatistics($detailedAnswers);

        return view('mock-tests.result', compact('attempt', 'result', 'detailedAnswers', 'statistics'));
    }

    /**
     * Show test review
     */
    public function review(TestAttempt $attempt): View
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$attempt->isCompleted()) {
            return redirect()->route('mock-tests.take', $attempt->id)
                ->with('info', 'Please complete the test first.');
        }

        $attempt = $this->mockTestService->getTestAttemptWithDetails($attempt->id);
        $questionNavigation = $this->getQuestionNavigation($attempt);

        return view('mock-tests.review', compact('attempt', 'questionNavigation'));
    }

    /**
     * Resume test attempt
     */
    public function resume(TestAttempt $attempt): RedirectResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $attempt = $this->mockTestService->resumeTestAttempt($attempt);

            return redirect()->route('mock-tests.take', $attempt->id)
                ->with('success', 'Test resumed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show user's test history
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 20);

        $attempts = $this->mockTestService->getUserTestAttempts($user, $perPage);
        $statistics = $this->mockTestService->getUserTestStatistics($user);

        return view('mock-tests.history', compact('attempts', 'statistics'));
    }

    /**
     * Show leaderboard
     */
    public function leaderboard(Test $test): View
    {
        $leaderboard = $this->mockTestService->getTestLeaderboard($test->id, 100);

        return view('mock-tests.leaderboard', compact('test', 'leaderboard'));
    }

    /**
     * Bookmark question
     */
    public function bookmark(TestAttempt $attempt, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
                'message' => 'Question bookmarked successfully',
                'bookmark' => $bookmark,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get question navigation data
     */
    private function getQuestionNavigation(TestAttempt $attempt): array
    {
        $navigation = [];
        $answers = $attempt->answers()->pluck('selected_option_id', 'question_id')->toArray();
        $bookmarks = $attempt->bookmarks()->pluck('question_id')->toArray();
        $markedForReview = $attempt->answers()
            ->where('is_marked_for_review', true)
            ->pluck('question_id')
            ->toArray();

        foreach ($attempt->question_ids as $index => $questionId) {
            $navigation[] = [
                'question_id' => $questionId,
                'index' => $index + 1,
                'answered' => isset($answers[$questionId]),
                'bookmarked' => in_array($questionId, $bookmarks),
                'marked_for_review' => in_array($questionId, $markedForReview),
            ];
        }

        return $navigation;
    }

    /**
     * Calculate detailed statistics for result page
     */
    private function calculateDetailedStatistics($answers): array
    {
        $stats = [
            'total_questions' => $answers->count(),
            'answered' => 0,
            'unanswered' => 0,
            'correct' => 0,
            'wrong' => 0,
            'marked_for_review' => 0,
            'accuracy_percentage' => 0,
            'time_spent_per_question' => 0,
            'subject_wise' => [],
            'difficulty_wise' => [],
        ];

        foreach ($answers as $answer) {
            $question = $answer->question;

            if ($answer->selected_option_id) {
                $stats['answered']++;
                if ($answer->isCorrect()) {
                    $stats['correct']++;
                } else {
                    $stats['wrong']++;
                }
            } else {
                $stats['unanswered']++;
            }

            if ($answer->is_marked_for_review) {
                $stats['marked_for_review']++;
            }

            // Subject-wise stats
            $subject = $question->chapter->topic->subject->name;
            if (!isset($stats['subject_wise'][$subject])) {
                $stats['subject_wise'][$subject] = ['correct' => 0, 'wrong' => 0, 'total' => 0];
            }
            $stats['subject_wise'][$subject]['total']++;
            if ($answer->selected_option_id && $answer->isCorrect()) {
                $stats['subject_wise'][$subject]['correct']++;
            } elseif ($answer->selected_option_id) {
                $stats['subject_wise'][$subject]['wrong']++;
            }

            // Difficulty-wise stats
            $difficulty = $question->difficulty_level;
            if (!isset($stats['difficulty_wise'][$difficulty])) {
                $stats['difficulty_wise'][$difficulty] = ['correct' => 0, 'wrong' => 0, 'total' => 0];
            }
            $stats['difficulty_wise'][$difficulty]['total']++;
            if ($answer->selected_option_id && $answer->isCorrect()) {
                $stats['difficulty_wise'][$difficulty]['correct']++;
            } elseif ($answer->selected_option_id) {
                $stats['difficulty_wise'][$difficulty]['wrong']++;
            }
        }

        $stats['accuracy_percentage'] = $stats['answered'] > 0
            ? round(($stats['correct'] / $stats['answered']) * 100, 2)
            : 0;

        return $stats;
    }
}