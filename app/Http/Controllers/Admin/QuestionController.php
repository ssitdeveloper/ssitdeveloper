<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use App\Models\Chapter;
use App\Enums\DifficultyLevel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Question::class);

        $query = Question::with('chapter.topic.subject');

        // Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where('question_text', 'like', "%{$search}%")
                  ->orWhere('explanation', 'like', "%{$search}%");
        }

        // Filter by difficulty
        if ($request->has('difficulty') && $request->filled('difficulty')) {
            $query->where('difficulty_level', $request->input('difficulty'));
        }

        // Filter by chapter
        if ($request->has('chapter') && $request->filled('chapter')) {
            $query->where('chapter_id', $request->input('chapter'));
        }

        // Filter by topic (via chapter)
        if ($request->has('topic') && $request->filled('topic')) {
            $query->whereHas('chapter', function ($q) {
                $q->where('topic_id', request('topic'));
            });
        }

        // Filter by subject (via chapter > topic)
        if ($request->has('subject') && $request->filled('subject')) {
            $query->whereHas('chapter.topic', function ($q) {
                $q->where('subject_id', request('subject'));
            });
        }

        // Filter by published status
        if ($request->has('published') && $request->filled('published')) {
            $query->where('is_published', $request->input('published') === 'published');
        }

        $questions = $query->paginate(15);

        return view('admin.questions.index', [
            'questions' => $questions,
            'difficulties' => DifficultyLevel::cases(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Question::class);
        $chapters = Chapter::with('topic.subject')->get();

        return view('admin.questions.create', [
            'chapters' => $chapters,
            'difficulties' => DifficultyLevel::cases(),
        ]);
    }

    public function store(StoreQuestionRequest $request)
    {
        $this->authorize('create', Question::class);
        $validated = $request->validated();

        $question = Question::create($validated);

        // Store options
        if ($request->has('options')) {
            foreach ($request->input('options') as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'] ?? false,
                    'order_by' => $index,
                ]);
            }
        }

        return redirect()->route('admin.questions.index')->with('success', 'Question created');
    }

    public function show(Question $question)
    {
        $this->authorize('view', $question);
        $question->load('chapter.topic.subject', 'options', 'tests');
        return view('admin.questions.show', ['question' => $question]);
    }

    public function edit(Question $question)
    {
        $this->authorize('update', $question);
        $chapters = Chapter::with('topic.subject')->get();

        return view('admin.questions.edit', [
            'question' => $question->load('options'),
            'chapters' => $chapters,
            'difficulties' => DifficultyLevel::cases(),
        ]);
    }

    public function update(StoreQuestionRequest $request, Question $question)
    {
        $this->authorize('update', $question);
        $validated = $request->validated();

        $question->update($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated');
    }

    public function destroy(Question $question)
    {
        $this->authorize('delete', $question);
        $question->delete();

        return redirect()->route('admin.questions.index')->with('success', 'Question deleted');
    }

    public function import()
    {
        $this->authorize('import', Question::class);
        return view('admin.questions.import');
    }

    public function export()
    {
        $this->authorize('export', Question::class);

        $questions = Question::with('options', 'chapter')
            ->where('is_published', true)
            ->get();

        // Sanitize output headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="questions_' . now()->format('Y-m-d_H-i-s') . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $csv = fopen('php://output', 'w');
        fputcsv($csv, ['Question', 'Chapter', 'Difficulty', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct', 'Explanation']);

        foreach ($questions as $question) {
            fputcsv($csv, [
                $question->question_text,
                $question->chapter->name,
                $question->difficulty_level->value,
                // Options would go here
            ]);
        }

        fclose($csv);
        exit;
    }
}
