<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->hasActiveSubscription()) {
            return redirect()->route('student.subscription.plans')
                ->with('error', 'Please subscribe to access this content.');
        }

        return $next($request);
    }
}
