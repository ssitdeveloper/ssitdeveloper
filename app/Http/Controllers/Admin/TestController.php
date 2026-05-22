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
}
