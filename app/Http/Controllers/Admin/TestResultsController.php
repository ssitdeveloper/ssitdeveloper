<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use DB;

class TestResultsController extends Controller
{
    /**
     * Show results dashboard with summary stats
     */
    public function dashboard(Request $request)
    {
        // Summary statistics
        $stats = [
            'total_attempts' => TestAttempt::count(),
            'completed_attempts' => TestAttempt::where('status', 'completed')->count(),
            'avg_score' => TestAttempt::where('status', 'completed')->avg('score') ?? 0,
            'total_unique_students' => TestAttempt::distinct('user_id')->count(),
            'total_tests' => Test::count(),
        ];

        // Attempts over time (last 30 days)
        $attempts_by_day = TestAttempt::where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->orderBy('date')
            ->get();

        // Top 10 tests by attempts
        $top_tests = Test::withCount('attempts')
            ->orderBy('attempts_count', 'desc')
            ->limit(10)
            ->get();

        // Score distribution
        $score_ranges = [
            '0-25' => TestAttempt::where('status', 'completed')->whereBetween('score', [0, 25])->count(),
            '25-50' => TestAttempt::where('status', 'completed')->whereBetween('score', [25, 50])->count(),
            '50-75' => TestAttempt::where('status', 'completed')->whereBetween('score', [50, 75])->count(),
            '75-100' => TestAttempt::where('status', 'completed')->whereBetween('score', [75, 100])->count(),
        ];

        // Completion rate
        $total_attempts = TestAttempt::count();
        $completion_stats = [
            'completed' => TestAttempt::where('status', 'completed')->count() / max(1, $total_attempts) * 100,
            'in_progress' => TestAttempt::where('status', 'in_progress')->count() / max(1, $total_attempts) * 100,
            'expired' => TestAttempt::where('status', 'expired')->count() / max(1, $total_attempts) * 100,
            'abandoned' => TestAttempt::where('status', 'abandoned')->count() / max(1, $total_attempts) * 100,
        ];

        return view('admin.results.dashboard', compact(
            'stats',
            'attempts_by_day',
            'top_tests',
            'score_ranges',
            'completion_stats'
        ));
    }

    /**
     * List all test attempts with filters
     */
    public function attempts(Request $request)
    {
        $query = TestAttempt::with('user', 'test');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Filter by student
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by test
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->input('test_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by score range
        if ($request->filled('score_min')) {
            $query->where('score', '>=', $request->input('score_min'));
        }
        if ($request->filled('score_max')) {
            $query->where('score', '<=', $request->input('score_max'));
        }

        $attempts = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $users = User::where('role', 'STUDENT')->pluck('name', 'id');
        $tests = Test::pluck('title', 'id');
        $statuses = ['completed', 'in_progress', 'expired', 'abandoned'];

        return view('admin.results.attempts', compact('attempts', 'users', 'tests', 'statuses'));
    }

    /**
     * Show all attempts by a specific student
     */
    public function studentDetail(User $student)
    {
        $attempts = $student->testAttempts()
            ->with('test')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Student statistics
        $stats = [
            'total_attempts' => $student->testAttempts()->count(),
            'completed_attempts' => $student->testAttempts()->where('status', 'completed')->count(),
            'avg_score' => $student->testAttempts()->where('status', 'completed')->avg('score') ?? 0,
            'best_score' => $student->testAttempts()->where('status', 'completed')->max('score') ?? 0,
            'worst_score' => $student->testAttempts()->where('status', 'completed')->min('score') ?? 0,
        ];

        return view('admin.results.student-detail', compact('student', 'attempts', 'stats'));
    }

    /**
     * Show detailed view of a single attempt
     */
    public function attemptDetail(TestAttempt $attempt)
    {
        $attempt->load('user', 'test', 'answers.question.options');

        // Calculate detailed results
        $questions = $attempt->test->questions;
        $attempt_questions = json_decode($attempt->question_ids, true);

        $results = [];
        $total_correct = 0;
        $total_marked = 0;

        foreach ($attempt_questions as $question_id) {
            $question = $questions->find($question_id);
            if (!$question) continue;

            $answer = $attempt->answers()->where('question_id', $question_id)->first();
            $selected_option_id = $answer?->selected_option_id;
            $correct_option = $question->options()->where('is_correct', true)->first();

            $is_correct = $selected_option_id && $correct_option && $selected_option_id === $correct_option->id;
            $is_answered = $selected_option_id !== null;

            if ($is_answered) $total_marked++;
            if ($is_correct) $total_correct++;

            $results[] = [
                'question' => $question,
                'selected_option_id' => $selected_option_id,
                'correct_option_id' => $correct_option?->id,
                'is_correct' => $is_correct,
                'is_answered' => $is_answered,
                'answered_at' => $answer?->answered_at,
                'time_spent_seconds' => $answer ? $answer->answered_at->diffInSeconds($answer->created_at) : 0,
            ];
        }

        $stats = [
            'total_questions' => count($attempt_questions),
            'questions_answered' => $total_marked,
            'questions_correct' => $total_correct,
            'questions_unanswered' => count($attempt_questions) - $total_marked,
            'accuracy_percentage' => $total_marked > 0 ? ($total_correct / $total_marked) * 100 : 0,
            'duration_minutes' => $attempt->started_at && $attempt->submitted_at
                ? $attempt->submitted_at->diffInMinutes($attempt->started_at)
                : 0,
        ];

        return view('admin.results.attempt-detail', compact('attempt', 'results', 'stats'));
    }

    /**
     * Get problematic questions (most answered wrong across all attempts)
     */
    public function problematicQuestions(Request $request)
    {
        $limit = $request->input('limit', 20);

        $problems = DB::table('test_attempt_answers as taa')
            ->join('questions as q', 'taa.question_id', '=', 'q.id')
            ->join('options as o', 'o.is_correct', '=', DB::raw('true'))
            ->select(
                'q.id',
                'q.question_text',
                DB::raw('COUNT(taa.id) as total_answers'),
                DB::raw('SUM(CASE WHEN taa.selected_option_id = o.id THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('o.question_id', DB::raw('q.id'))
            ->groupBy('q.id', 'q.question_text')
            ->orderBy(DB::raw('(100 * SUM(CASE WHEN taa.selected_option_id = o.id THEN 1 ELSE 0 END) / COUNT(taa.id))'))
            ->limit($limit)
            ->get();

        foreach ($problems as $problem) {
            $problem->accuracy_percentage = ($problem->correct_answers / max(1, $problem->total_answers)) * 100;
        }

        return view('admin.results.problematic-questions', compact('problems'));
    }

    /**
     * Export attempt results to CSV
     */
    public function exportAttempts(Request $request)
    {
        $query = TestAttempt::with('user', 'test');

        // Apply same filters as attempts()
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->input('test_id'));
        }

        $attempts = $query->get();

        $csv = "ID,Student Name,Student Email,Test Name,Score,Status,Questions Answered,Started At,Submitted At\n";

        foreach ($attempts as $attempt) {
            $total_marked = count(json_decode($attempt->question_ids, true) ?? []);
            $csv .= implode(',', [
                $attempt->id,
                '"' . $attempt->user->name . '"',
                $attempt->user->email,
                '"' . $attempt->test->title . '"',
                $attempt->score ?? 0,
                $attempt->status,
                $total_marked,
                $attempt->started_at,
                $attempt->submitted_at,
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="test-results-' . now()->format('Y-m-d') . '.csv"');
    }
}
