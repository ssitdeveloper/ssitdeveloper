<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function getUserAnalytics()
    {
        $stats = $this->analyticsService->getUserDashboardStats(auth()->user());

        return response()->json($stats);
    }

    public function getLeaderboard()
    {
        $leaderboard = $this->analyticsService->getLeaderboard(10);

        return response()->json($leaderboard);
    }

    public function getSubjectWiseAnalytics()
    {
        $analytics = $this->analyticsService->getSubjectWiseAnalytics(auth()->user());

        return response()->json($analytics);
    }
}
