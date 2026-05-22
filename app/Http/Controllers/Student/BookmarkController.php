<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Question;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = auth()->user()->bookmarks()
            ->with('question.chapter')
            ->latest()
            ->paginate(15);

        return view('student.bookmarks.index', ['bookmarks' => $bookmarks]);
    }

    public function store(Request $request)
    {
        $request->validate(['question_id' => 'required|exists:questions,id']);

        $bookmark = auth()->user()->bookmarks()
            ->firstOrCreate(['question_id' => $request->question_id]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'bookmarked' => $bookmark->id]);
        }

        return redirect()->back()->with('success', 'Question bookmarked');
    }

    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('delete', $bookmark);
        $bookmark->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Bookmark removed');
    }
}
