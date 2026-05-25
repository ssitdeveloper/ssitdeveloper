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
        $attempts = auth()->user()->testAttempts;

        return [
            'total_tests' => $attempts->count(),
            'passed' => $attempts->where('status', 'passed')->count(),
            'failed' => $attempts->where('status', 'failed')->count(),
            'avg_score' => $attempts->avg('marks_obtained') ?? 0,
            'highest_score' => $attempts->max('marks_obtained') ?? 0,
            'lowest_score' => $attempts->min('marks_obtained') ?? 0,
        ];
    }
}
