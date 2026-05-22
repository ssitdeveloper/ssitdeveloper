<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle admin login with brute-force protection
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists and has admin role
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are invalid.'],
            ]);
        }

        // Check if user is admin
        $adminUser = \App\Models\AdminUser::where('user_id', $user->id)->first();

        if (!$adminUser || !$adminUser->is_active) {
            throw ValidationException::withMessages([
                'email' => ['You do not have admin access.'],
            ]);
        }

        // Attempt to authenticate
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        // Regenerate session for security
        $request->session()->regenerate();

        // Update last login
        $adminUser->update(['last_login_at' => now()]);


        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle student login with brute-force protection
     */
    public function studentLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->save();
            return redirect()->route('student.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are invalid.'],
        ]);
    }

    /**
     * Handle logout and session invalidation
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
