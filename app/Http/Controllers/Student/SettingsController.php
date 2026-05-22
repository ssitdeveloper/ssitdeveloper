<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('student.settings.index', ['user' => $user]);
    }

    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'test_reminders' => 'boolean',
            'course_updates' => 'boolean',
            'promotional_emails' => 'boolean',
        ]);

        auth()->user()->update([
            'email_notifications' => $request->has('email_notifications'),
            'test_reminders' => $request->has('test_reminders'),
            'course_updates' => $request->has('course_updates'),
            'promotional_emails' => $request->has('promotional_emails'),
        ]);

        return redirect()->back()->with('success', 'Notification settings updated');
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:en,hi',
            'theme' => 'required|in:light,dark',
            'timezone' => 'required|timezone',
        ]);

        auth()->user()->update($validated);

        return redirect()->back()->with('success', 'Preferences updated');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:12|confirmed|regex:/[a-z]|regex:/[A-Z]|regex:/[0-9]|regex:/[!@#$%^&*]/',
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully');
    }
}
