<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // If user is authenticated, redirect to appropriate dashboard
        if (Auth::check()) {
            if (Auth::user()->role->value === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('student.dashboard');
        }

        // Otherwise show landing page
        return view('home');
    }
}
