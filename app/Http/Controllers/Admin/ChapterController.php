<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Topic;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $query = Chapter::with('topic.subject')->latest();

        // Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by topic
        if ($request->has('topic') && $request->filled('topic')) {
            $query->where('topic_id', $request->input('topic'));
        }

        // Filter by subject (via topic)
        if ($request->has('subject') && $request->filled('subject')) {
            $query->whereHas('topic', function ($q) {
                $q->where('subject_id', request('subject'));
            });
        }

        $chapters = $query->paginate(15);

        return view('admin.chapters.index', [
            'chapters' => $chapters,
            'topics' => Topic::with('subject')->get()
        ]);
    }

    public function create()
    {
        $topics = Topic::with('subject')->get();
        return view('admin.chapters.create', ['topics' => $topics]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        Chapter::create($validated);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter created successfully');
    }

    public function show(Chapter $chapter)
    {
        $chapter->load('topic.subject', 'questions');
        return view('admin.chapters.show', ['chapter' => $chapter]);
    }

    public function edit(Chapter $chapter)
    {
        $topics = Topic::with('subject')->get();
        return view('admin.chapters.edit', ['chapter' => $chapter, 'topics' => $topics]);
    }

    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $chapter->update($validated);

        return redirect()->route('admin.chapters.show', $chapter)
            ->with('success', 'Chapter updated successfully');
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter deleted successfully');
    }
}
