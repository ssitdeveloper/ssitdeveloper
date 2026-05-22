@extends('layouts.student')

@section('title', 'Settings')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Account Settings</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: var(--spacing-3); background: rgba(16, 185, 129, 0.1); color: #059669; padding: var(--spacing-3); border-radius: var(--radius-lg); border-left: 4px solid #059669;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; gap: var(--spacing-4); max-width: 600px;">
        <!-- Notification Settings -->
        <div class="student-card">
            <h2 style="margin-top: 0; color: var(--color-gray-900);">Notification Preferences</h2>
            <form method="POST" action="{{ route('student.settings.notifications') }}">
                @csrf
                <div style="display: grid; gap: var(--spacing-3);">
                    <label style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer; margin: 0;">
                        <input type="checkbox" name="email_notifications" @if(auth()->user()->email_notifications ?? true) checked @endif style="width: 20px; height: 20px;">
                        <span style="color: var(--color-gray-900);">Email Notifications</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer; margin: 0;">
                        <input type="checkbox" name="test_reminders" @if(auth()->user()->test_reminders ?? true) checked @endif style="width: 20px; height: 20px;">
                        <span style="color: var(--color-gray-900);">Test Reminders</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer; margin: 0;">
                        <input type="checkbox" name="course_updates" @if(auth()->user()->course_updates ?? true) checked @endif style="width: 20px; height: 20px;">
                        <span style="color: var(--color-gray-900);">Course Updates</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: var(--spacing-2); cursor: pointer; margin: 0;">
                        <input type="checkbox" name="promotional_emails" @if(auth()->user()->promotional_emails ?? false) checked @endif style="width: 20px; height: 20px;">
                        <span style="color: var(--color-gray-900);">Promotional Emails</span>
                    </label>
                </div>
                <button type="submit" style="margin-top: var(--spacing-3); padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                    Save Preferences
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="student-card">
            <h2 style="margin-top: 0; color: var(--color-gray-900);">Change Password</h2>
            <form method="POST" action="{{ route('student.settings.password') }}" style="display: grid; gap: var(--spacing-3);">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Current Password</label>
                    <input type="password" name="current_password" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">New Password</label>
                    <input type="password" name="password" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Min 12 characters with uppercase, lowercase, numbers & symbols</p>
                </div>
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Confirm Password</label>
                    <input type="password" name="password_confirmation" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                </div>
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
