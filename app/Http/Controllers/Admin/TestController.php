<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = Test::query();

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by active status
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $tests = $query->withCount('questions')->with('questions')->latest()->paginate(15);
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function show(Test $test)
    {
        $test->load('questions.chapter.topic.subject');
        return view('admin.tests.show', compact('test'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:tests',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'total_questions' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($request->input('title'));
        $validated['is_active'] = $request->has('is_active');
        $validated['subject_distribution'] = [];
        $validated['difficulty_distribution'] = [];

        Test::create($validated);

        return redirect()->route('admin.tests.index')
                       ->with('success', 'Test created successfully');
    }

    public function edit(Test $test)
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:tests,title,' . $test->id,
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'total_questions' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($request->input('title'));
        $validated['is_active'] = $request->has('is_active');

        $test->update($validated);

        return redirect()->route('admin.tests.index')
                       ->with('success', 'Test updated successfully');
    }

    public function destroy($id)
    {
        Test::destroy($id);
        return redirect()->route('admin.tests.index')->with('success', 'Test deleted successfully');
    }

    /**
     * Show questions assigned to test
     */
    public function questions(Test $test)
    {
        $test->load('questions.chapter.topic.subject', 'questions.options');
        $questions = $test->questions()
            ->orderBy('pivot_order', 'asc')
            ->paginate(20);

        return view('admin.tests.questions', compact('test', 'questions'));
    }

    /**
     * Search and add questions to test (AJAX)
     */
    public function searchQuestions(Request $request, Test $test)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:1',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'difficulty' => 'nullable|in:EASY,MEDIUM,HARD',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = \App\Models\Question::query()
            ->select('id', 'question_text', 'difficulty', 'subject_id')
            ->with('subject:id,name');

        if ($validated['q']) {
            $query->whereRaw("MATCH(question_text) AGAINST(? IN BOOLEAN MODE)", [$validated['q']]);
        }

        if ($validated['subject_id'] ?? null) {
            $query->where('subject_id', $validated['subject_id']);
        }

        if ($validated['difficulty'] ?? null) {
            $query->where('difficulty', $validated['difficulty']);
        }

        // Exclude questions already in this test
        $existing_ids = $test->questions()->pluck('id')->toArray();
        if ($existing_ids) {
            $query->whereNotIn('id', $existing_ids);
        }

        $questions = $query->limit($validated['limit'] ?? 20)->get();

        return response()->json($questions);
    }

    /**
     * Add questions to test
     */
    public function addQuestions(Request $request, Test $test)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);

        // Get current max order
        $maxOrder = $test->questions()->max('pivot_order') ?? 0;

        // Attach questions with order
        $attach_data = [];
        foreach ($validated['question_ids'] as $index => $question_id) {
            $attach_data[$question_id] = ['order' => $maxOrder + $index + 1];
        }

        $test->questions()->attach($attach_data);

        return response()->json([
            'status' => 'success',
            'message' => count($validated['question_ids']) . ' questions added to test',
        ]);
    }

    /**
     * Remove question from test
     */
    public function removeQuestion(Request $request, Test $test)
    {
        $validated = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
        ]);

        $test->questions()->detach($validated['question_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Question removed from test',
        ]);
    }

    /**
     * Reorder questions in test
     */
    public function reorderQuestions(Request $request, Test $test)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:questions,id',
        ]);

        // Update order in pivot table
        foreach ($validated['order'] as $index => $question_id) {
            $test->questions()->updateExistingPivot($question_id, ['order' => $index + 1]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Question order updated',
        ]);
    }

    /**
     * Publish test (make available to students)
     */
    public function publish(Test $test)
    {
        // Validate test has questions
        if ($test->questions()->count() === 0) {
            return redirect()->back()->with('error', 'Test must have at least one question');
        }

        $test->update([
            'is_active' => true,
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Test published successfully');
    }

    /**
     * Unpublish test (hide from students)
     */
    public function unpublish(Test $test)
    {
        $test->update([
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'Test unpublished successfully');
    }

    /**
     * Duplicate test
     */
    public function duplicate(Request $request, Test $test)
    {
        $new_test = $test->replicate();
        $new_test->title = $test->title . ' (Copy)';
        $new_test->slug = \Illuminate\Support\Str::slug($new_test->title);
        $new_test->is_active = false;
        $new_test->published_at = null;
        $new_test->save();

        // Duplicate questions relationship
        $test->questions()->get()->each(function ($question) use ($new_test) {
            $new_test->questions()->attach($question->id);
        });

        return redirect()->route('admin.tests.show', $new_test)
                        ->with('success', 'Test duplicated successfully');
    }

    /**
     * Get test statistics
     */
    public function statistics(Test $test)
    {
        $total_attempts = $test->attempts()->count();
        $completed_attempts = $test->attempts()->where('status', 'completed')->count();

        $stats = [
            'total_questions' => $test->questions()->count(),
            'total_attempts' => $total_attempts,
            'avg_score' => $test->attempts()->avg('score') ?? 0,
            'avg_time_minutes' => $test->attempts()->avg(\DB::raw('TIMESTAMPDIFF(MINUTE, started_at, submitted_at)')) ?? 0,
            'completion_rate' => $total_attempts > 0 ? ($completed_attempts / $total_attempts) : 0,
        ];

        return response()->json($stats);
    }
}
