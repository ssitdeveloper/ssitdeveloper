@extends('layouts.student')

@section('title', 'Settings')

@section('content')
    <div class="student-card" style="margin-bottom: var(--spacing-4);">
        <h2 style="margin-top: 0; font-size: var(--font-size-xl); margin-bottom: var(--spacing-3);">Account Settings</h2>
        <p style="color: var(--color-gray-600); margin: 0;">Manage your profile and preferences.</p>
    </div>

    <!-- Personal Information -->
    <div class="student-card">
        <h3 style="margin-top: 0; margin-bottom: var(--spacing-3);">Personal Information</h3>
        <form style="display: flex; flex-direction: column; gap: var(--spacing-3);">
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">Full Name</label>
                <input type="text" value="John Doe" class="form-input">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">Email Address</label>
                <input type="email" value="john@example.com" class="form-input">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">Phone Number</label>
                <input type="tel" value="+91 98765 43210" class="form-input">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">Target Exam</label>
                <select class="form-select">
                    <option>NEET UG</option>
                    <option>NEET PG</option>
                    <option>AIIMS</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="student-card">
        <h3 style="margin-top: 0; margin-bottom: var(--spacing-3);">Change Password</h3>
        <form style="display: flex; flex-direction: column; gap: var(--spacing-3);">
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">Current Password</label>
                <input type="password" placeholder="Enter current password" class="form-input">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">New Password</label>
                <input type="password" placeholder="Enter new password" class="form-input">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: bold; color: var(--color-gray-700);">Confirm Password</label>
                <input type="password" placeholder="Confirm new password" class="form-input">
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>

    <!-- Notification Preferences -->
    <div class="student-card">
        <h3 style="margin-top: 0; margin-bottom: var(--spacing-3);">Notification Preferences</h3>
        <div style="display: flex; flex-direction: column; gap: var(--spacing-2);">
            <label class="form-checkbox" style="display: flex; align-items: center; gap: var(--spacing-2);">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span>Email notifications for test reminders</span>
            </label>

            <label class="form-checkbox" style="display: flex; align-items: center; gap: var(--spacing-2);">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span>Push notifications for course updates</span>
            </label>

            <label class="form-checkbox" style="display: flex; align-items: center; gap: var(--spacing-2);">
                <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;">
                <span>Weekly performance report</span>
            </label>

            <label class="form-checkbox" style="display: flex; align-items: center; gap: var(--spacing-2);">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span>Subscription expiration reminders</span>
            </label>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="student-card" style="border-left: 4px solid var(--color-danger);">
        <h3 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-danger);">Danger Zone</h3>
        <div style="padding: var(--spacing-3); background-color: #FEF2F2; border-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="color: var(--color-danger);">Delete Account</strong>
                <p style="margin: var(--spacing-1) 0 0 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Permanently delete your account and all associated data</p>
            </div>
            <button class="btn btn-danger">Delete Account</button>
        </div>
    </div>
@endsection
