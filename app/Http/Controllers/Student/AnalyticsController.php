<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analyticsService)
    {
        $user = auth()->user();
        $stats = $analyticsService->getUserDashboardStats($user);
        $subjectWiseAnalytics = $analyticsService->getSubjectWiseAnalytics($user);

        return view('student.analytics', [
            'stats' => $stats,
            'subjectWiseAnalytics' => $subjectWiseAnalytics,
        ]);
    }

    public function leaderboard(AnalyticsService $analyticsService)
    {
        $leaderboard = $analyticsService->getLeaderboard(20);

        return view('student.leaderboard', ['leaderboard' => $leaderboard]);
    }

    public function weakTopics()
    {
        $weakTopics = \App\Models\UserWeakTopic::where('user_id', auth()->id())
            ->with('topic.subject', 'topic.chapters.questions')
            ->orderBy('weak_score', 'asc')
            ->paginate(15);

        return view('student.analytics.weak-topics', ['weakTopics' => $weakTopics]);
    }

    public function progress()
    {
        $user = auth()->user();

        $learningProgress = $user->learningProgress()
            ->with('chapter.topic.subject')
            ->latest()
            ->paginate(20);

        $stats = [
            'chapters_started' => $learningProgress->where('progress', '>', 0)->count(),
            'chapters_completed' => $learningProgress->where('progress', '=', 100)->count(),
            'avg_progress' => round($learningProgress->avg('progress') ?? 0),
        ];

        return view('student.analytics.progress', [
            'learningProgress' => $learningProgress,
            'stats' => $stats,
        ]);
    }

    public function testHistory()
    {
        $attempts = auth()->user()->testAttempts()
            ->with('test')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_tests' => $attempts->total(),
            'passed' => $attempts->where('status', 'passed')->count(),
            'avg_score' => round($attempts->avg('marks_obtained') ?? 0),
        ];

        return view('student.analytics.test-history', [
            'attempts' => $attempts,
            'stats' => $stats,
        ]);
    }
}
