<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('topics');

        // Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Sort
        if ($request->has('sort') && $request->input('sort') === 'name') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderBy('order_by', 'asc');
        }

        $subjects = $query->paginate(15);

        return view('admin.subjects.index', ['subjects' => $subjects]);
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subjects',
            'description' => 'nullable',
            'icon' => 'nullable',
            'color' => 'required',
            'order_by' => 'required|integer',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created');
    }

    public function show(Subject $subject)
    {
        $subject->load('topics.chapters.questions');
        return view('admin.subjects.show', ['subject' => $subject]);
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', ['subject' => $subject]);
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subjects,name,' . $subject->id,
            'description' => 'nullable',
            'icon' => 'nullable',
            'color' => 'required',
            'order_by' => 'required|integer',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.show', $subject)->with('success', 'Subject updated');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted');
    }
}
