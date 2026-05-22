<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class EnsureStudentRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== UserRole::STUDENT) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
