<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with('user', 'payment')->latest();

        // Search by user name or email
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by plan
        if ($request->has('plan') && $request->filled('plan')) {
            $query->where('plan', $request->input('plan'));
        }

        // Filter by status
        if ($request->has('status') && $request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('status', 'ACTIVE')->where('expires_at', '>', now());
            } elseif ($request->input('status') === 'expired') {
                $query->where(function ($q) {
                    $q->where('status', 'EXPIRED')->orWhere('expires_at', '<=', now());
                });
            } elseif ($request->input('status') === 'cancelled') {
                $query->where('status', 'CANCELLED');
            }
        }

        $subscriptions = $query->paginate(15);

        return view('admin.subscriptions.index', ['subscriptions' => $subscriptions]);
    }

    public function show(Subscription $subscription)
    {
        return view('admin.subscriptions.show', [
            'subscription' => $subscription->load('user', 'payment')
        ]);
    }
}
