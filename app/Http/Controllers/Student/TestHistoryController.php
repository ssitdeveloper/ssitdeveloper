<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

class TestHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->testAttempts()
            ->with('test', 'answers', 'result')
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $attempts = $query->paginate(15);

        return view('student.test-history.index', [
            'attempts' => $attempts,
            'stats' => $this->getStats(),
        ]);
    }

    public function show(TestAttempt $attempt)
    {
        $this->authorize('view', $attempt);
        $attempt->load('test', 'answers.question', 'answers.option');

        return view('student.test-history.show', ['attempt' => $attempt]);
    }

    public function getStats()
    {
        $attempts = auth()->user()->testAttempts()->with('result')->get();

        // Calculate scores from related results
        $scores = $attempts->map(function($attempt) {
            return $attempt->result ? (float)$attempt->result->percentage : 0;
        });

        $passedCount = $attempts->filter(function($attempt) {
            return $attempt->result && (float)$attempt->result->percentage >= 60;
        })->count();

        return [
            'total_tests' => $attempts->count(),
            'passed' => $passedCount,
            'failed' => $attempts->count() - $passedCount,
            'avg_score' => $scores->count() > 0 ? round($scores->average(), 1) : 0,
            'highest_score' => $scores->count() > 0 ? $scores->max() : 0,
            'lowest_score' => $scores->count() > 0 ? $scores->min() : 0,
        ];
    }
}
