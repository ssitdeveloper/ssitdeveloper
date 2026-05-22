<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\QuestionService;

class QuestionController extends Controller
{
    public function __construct(private QuestionService $questionService) {}

    public function practice()
    {
        $subjects = \App\Models\Subject::with('topics')->get();

        return view('student.practice', ['subjects' => $subjects]);
    }

    public function practiceByChapter($chapterId)
    {
        $questions = $this->questionService->getQuestionsForChapter($chapterId);

        return view('student.practice-chapter', ['questions' => $questions]);
    }

    public function submitAnswer()
    {
        $validated = request()->validate([
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'required|exists:options,id',
        ]);

        $question = Question::findOrFail($validated['question_id']);
        $isCorrect = $question->options()
            ->where('id', $validated['option_id'])
            ->where('is_correct', true)
            ->exists();

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_option' => $question->getCorrectOption(),
            'explanation' => $question->explanation,
        ]);
    }

    public function bookmarks()
    {
        $bookmarks = $this->questionService->getUserBookmarks(auth()->user());

        return view('student.bookmarks', ['bookmarks' => $bookmarks]);
    }

    public function addBookmark()
    {
        $validated = request()->validate(['question_id' => 'required|exists:questions,id']);

        $question = Question::findOrFail($validated['question_id']);
        $this->questionService->bookmarkQuestion(auth()->user(), $question);

        return response()->json(['message' => 'Bookmarked']);
    }

    public function removeBookmark($id)
    {
        $question = Question::findOrFail($id);
        $this->questionService->removeBookmark(auth()->user(), $question);

        return response()->json(['message' => 'Bookmark removed']);
    }
}
