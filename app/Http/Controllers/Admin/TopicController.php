<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subject;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $query = Topic::with('subject')->latest();

        // Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by subject
        if ($request->has('subject') && $request->filled('subject')) {
            $query->where('subject_id', $request->input('subject'));
        }

        $topics = $query->paginate(15);

        return view('admin.topics.index', ['topics' => $topics, 'subjects' => Subject::all()]);
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.topics.create', ['subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Topic::create($validated);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic created successfully');
    }

    public function show(Topic $topic)
    {
        $topic->load('subject', 'chapters.questions');
        return view('admin.topics.show', ['topic' => $topic]);
    }

    public function edit(Topic $topic)
    {
        $subjects = Subject::all();
        return view('admin.topics.edit', ['topic' => $topic, 'subjects' => $subjects]);
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $topic->update($validated);

        return redirect()->route('admin.topics.show', $topic)
            ->with('success', 'Topic updated successfully');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully');
    }
}
