<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Services\TestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TestService $testService) {}

    public function index()
    {
        $tests = Test::published()
            ->latest()
            ->paginate(10);

        return view('student.tests', ['tests' => $tests]);
    }

    public function show($slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();

        $userAttempts = $test->attempts()
            ->where('user_id', auth()->id())
            ->count();

        return view('student.test-show', [
            'test' => $test,
            'userAttempts' => $userAttempts,
        ]);
    }

    public function start(Request $request, $slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();

        $this->authorize('create', TestAttempt::class);

        $attempt = $this->testService->startTest(auth()->user(), $test);

        return redirect()->route('student.tests.attempt', [
            'slug' => $test->slug,
            'attemptId' => $attempt->id,
        ]);
    }

    public function attempt($slug, $attemptId)
    {
        $test = Test::where('slug', $slug)->firstOrFail();
        $attempt = $test->attempts()->findOrFail($attemptId);

        $this->authorize('view', $attempt);

        // Get the questions for this attempt in the randomized order
        $questions = [];
        if (!empty($attempt->question_ids)) {
            $questions = \App\Models\Question::whereIn('id', $attempt->question_ids)
                ->with('options')
                ->get()
                ->keyBy('id')
                ->sortBy(function($q) use ($attempt) {
                    return array_search($q->id, $attempt->question_ids);
                })
                ->values();
        }

        return view('student.test-attempt', [
            'test' => $test,
            'attempt' => $attempt,
            'questions' => $questions,
        ]);
    }

    public function submitAnswer(Request $request, $slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();
        $attempt = $test->attempts()->findOrFail($request->input('attempt_id'));

        $this->authorize('recordAnswer', $attempt);

        // Get all answers from the form
        $answers = $request->input('answers', []);

        // Save all answers
        foreach ($answers as $questionId => $optionId) {
            if ($optionId) {
                $this->testService->submitAnswer(
                    $attempt,
                    (int)$questionId,
                    (int)$optionId,
                    0 // time_spent - not tracked in this version
                );
            }
        }

        // Complete the test
        $this->testService->completeTest($attempt);

        // Redirect to results page
        return redirect()->route('student.tests.result', [
            'slug' => $test->slug,
            'attemptId' => $attempt->id,
        ]);
    }

    public function result($slug, $attemptId)
    {
        $test = Test::where('slug', $slug)->firstOrFail();
        $attempt = $test->attempts()->findOrFail($attemptId);

        $this->authorize('view', $attempt);

        $this->testService->completeTest($attempt);
        $results = $this->testService->getResults($attempt);

        return view('student.test-result', [
            'test' => $test,
            'attempt' => $attempt,
            'results' => $results,
        ]);
    }
}
