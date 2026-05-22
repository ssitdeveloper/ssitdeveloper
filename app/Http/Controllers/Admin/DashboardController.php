<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
        $totalRevenue = DB::table('payment_transactions')
            ->where('status', 'completed')
            ->sum('amount');

        $recentPayments = DB::table('payment_transactions')
            ->join('users', 'payment_transactions.user_id', '=', 'users.id')
            ->join('subscription_plans', 'payment_transactions.subscription_plan_id', '=', 'subscription_plans.id')
            ->select('payment_transactions.*', 'users.name', 'subscription_plans.name as plan_name')
            ->where('payment_transactions.status', 'completed')
            ->latest('payment_transactions.created_at')
            ->limit(10)
            ->get();

        $monthlyRevenue = DB::table('payment_transactions')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $newStudents = User::where('role', 'student')
            ->whereDate('created_at', today())
            ->count();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'activeSubscriptions' => $activeSubscriptions,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'newStudents' => $newStudents,
            'recentPayments' => $recentPayments,
        ]);
    }
}
