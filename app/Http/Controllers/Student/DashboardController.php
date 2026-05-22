<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Test;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get user subscription
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        // Get recent test attempts
        $recentTests = DB::table('test_attempts')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Get available tests
        $availableTests = Test::where('is_active', true)->limit(5)->get();

        // Calculate basic stats
        $stats = [
            'subscription_status' => $subscription ? $subscription->status->value : 'none',
            'subscription_expires' => $subscription ? $subscription->expires_at : null,
            'total_attempts' => DB::table('test_attempts')->where('user_id', $user->id)->count(),
            'completed_tests' => DB::table('test_attempts')->where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('student.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'subscription' => $subscription,
            'recentTests' => $recentTests,
            'availableTests' => $availableTests,
        ]);
    }
}
