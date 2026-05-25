<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    /**
     * List all past test results for the student
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = $student->testAttempts()
            ->with('test')
            ->where('status', 'completed');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Filter by test
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->input('test_id'));
        }

        // Filter by score range
        if ($request->filled('score_min')) {
            $query->where('score', '>=', $request->input('score_min'));
        }
        if ($request->filled('score_max')) {
            $query->where('score', '<=', $request->input('score_max'));
        }

        $results = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('student.results.index', compact('results'));
    }

    /**
     * Show detailed result of a single test attempt
     */
    public function show(TestAttempt $attempt)
    {
        // Authorize: student can only view their own results
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $attempt->load('test', 'user', 'answers.question.options', 'answers.question.chapter.topic.subject');

        // Calculate detailed results
        $questions = $attempt->test->questions;
        $attempt_questions = json_decode($attempt->question_ids, true);

        $results_by_question = [];
        $results_by_subject = [];
        $results_by_chapter = [];

        $total_correct = 0;
        $total_marked = 0;
        $total_time_seconds = 0;

        foreach ($attempt_questions as $index => $question_id) {
            $question = $questions->find($question_id);
            if (!$question) continue;

            $answer = $attempt->answers()->where('question_id', $question_id)->first();
            $selected_option_id = $answer?->selected_option_id;
            $correct_option = $question->options()->where('is_correct', true)->first();

            $is_correct = $selected_option_id && $correct_option && $selected_option_id === $correct_option->id;
            $is_answered = $selected_option_id !== null;

            if ($is_answered) $total_marked++;
            if ($is_correct) $total_correct++;

            $time_spent = $answer ? $answer->answered_at->diffInSeconds($answer->created_at) : 0;
            $total_time_seconds += $time_spent;

            // Question result
            $question_result = [
                'index' => $index + 1,
                'question' => $question,
                'question_id' => $question_id,
                'selected_option_id' => $selected_option_id,
                'correct_option_id' => $correct_option?->id,
                'is_correct' => $is_correct,
                'is_answered' => $is_answered,
                'time_spent_seconds' => $time_spent,
                'subject_name' => $question->chapter?->topic?->subject?->name ?? 'N/A',
                'chapter_name' => $question->chapter?->name ?? 'N/A',
            ];

            $results_by_question[] = $question_result;

            // Subject aggregation
            $subject = $question->chapter?->topic?->subject;
            if ($subject) {
                if (!isset($results_by_subject[$subject->id])) {
                    $results_by_subject[$subject->id] = [
                        'name' => $subject->name,
                        'total' => 0,
                        'correct' => 0,
                    ];
                }
                $results_by_subject[$subject->id]['total']++;
                if ($is_correct) $results_by_subject[$subject->id]['correct']++;
            }

            // Chapter aggregation
            $chapter = $question->chapter;
            if ($chapter) {
                if (!isset($results_by_chapter[$chapter->id])) {
                    $results_by_chapter[$chapter->id] = [
                        'name' => $chapter->name,
                        'total' => 0,
                        'correct' => 0,
                    ];
                }
                $results_by_chapter[$chapter->id]['total']++;
                if ($is_correct) $results_by_chapter[$chapter->id]['correct']++;
            }
        }

        // Calculate percentages
        foreach ($results_by_subject as &$subject) {
            $subject['accuracy'] = $subject['total'] > 0 ? ($subject['correct'] / $subject['total']) * 100 : 0;
        }
        unset($subject);

        foreach ($results_by_chapter as &$chapter) {
            $chapter['accuracy'] = $chapter['total'] > 0 ? ($chapter['correct'] / $chapter['total']) * 100 : 0;
        }
        unset($chapter);

        $stats = [
            'score' => $attempt->score ?? 0,
            'total_questions' => count($attempt_questions),
            'questions_answered' => $total_marked,
            'questions_correct' => $total_correct,
            'questions_unanswered' => count($attempt_questions) - $total_marked,
            'accuracy_percentage' => $total_marked > 0 ? ($total_correct / $total_marked) * 100 : 0,
            'duration_minutes' => ceil($total_time_seconds / 60),
            'avg_time_per_question_seconds' => count($attempt_questions) > 0 ? $total_time_seconds / count($attempt_questions) : 0,
        ];

        // Get class average for comparison
        $class_average = TestAttempt::where('test_id', $attempt->test_id)
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $stats['class_average'] = $class_average;
        $stats['score_difference'] = $stats['score'] - $class_average;

        return view('student.results.show', compact(
            'attempt',
            'results_by_question',
            'results_by_subject',
            'results_by_chapter',
            'stats'
        ));
    }

    /**
     * Review past test attempt (read-only)
     */
    public function review(TestAttempt $attempt)
    {
        // Authorization check
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Same as show() but in review mode (can't modify anything)
        return $this->show($attempt);
    }

    /**
     * Export result as PDF
     */
    public function exportPdf(TestAttempt $attempt)
    {
        // Authorization check
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $attempt->load('test', 'user', 'answers.question');

        $pdf = Pdf::loadView('student.results.pdf', [
            'attempt' => $attempt,
        ]);

        return $pdf->download('test-result-' . $attempt->id . '.pdf');
    }

    /**
     * Get recommendations based on weak topics
     */
    public function recommendations(TestAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $attempt->load('test', 'answers.question.chapter');

        // Find weak topics (less than 70% accuracy)
        $weak_topics = [];
        $chapter_performance = [];

        foreach ($attempt->answers as $answer) {
            $question = $answer->question;
            $chapter = $question->chapter;

            if (!$chapter) continue;

            if (!isset($chapter_performance[$chapter->id])) {
                $chapter_performance[$chapter->id] = [
                    'chapter' => $chapter,
                    'correct' => 0,
                    'total' => 0,
                ];
            }

            $chapter_performance[$chapter->id]['total']++;

            if ($answer->isCorrect()) {
                $chapter_performance[$chapter->id]['correct']++;
            }
        }

        // Find weak chapters
        foreach ($chapter_performance as $chapter_id => $perf) {
            $accuracy = ($perf['correct'] / max(1, $perf['total'])) * 100;
            if ($accuracy < 70) {
                $weak_topics[] = [
                    'chapter' => $perf['chapter'],
                    'accuracy' => $accuracy,
                    'suggested_questions' => \App\Models\Question::where('chapter_id', $chapter_id)
                        ->where('difficulty', '!=', 'EASY')
                        ->limit(5)
                        ->get(),
                ];
            }
        }

        return view('student.results.recommendations', compact('attempt', 'weak_topics'));
    }
}
