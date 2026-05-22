@extends('layouts.student')

@section('title', 'Student Dashboard - NEET LMS')

@section('content')
<div class="dashboard-content-wrapper">
    <!-- Key Stats -->
    <div class="stats-grid">
        <div class="stat-card">

            <div class="stat-label">Tests Attempted</div>
            <div class="stat-value">{{ $stats['total_attempts'] }}</div>
            <div class="stat-change">{{ $stats['completed_tests'] }} completed</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Subscription</div>
            <div class="stat-value">{{ ucfirst($stats['subscription_status']) }}</div>
            <div class="stat-change">
                @if($subscription && $subscription->expires_at)
                    Expires {{ $subscription->expires_at->format('M d') }}
                @else
                    No active plan
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Available Tests</div>
            <div class="stat-value">{{ $availableTests->count() }}</div>
            <div class="stat-change">Ready to take</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Account Type</div>
            <div class="stat-value">{{ ucfirst(auth()->user()->role->value) }}</div>
            <div class="stat-change">Active member</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card-section" style="margin-bottom: 3rem;">
        <div class="card-header">
            <h3>Quick Actions</h3>
        </div>
        <div class="card-body">
            <div class="quick-links">
                <a href="{{ route('student.tests') }}" class="quick-link-btn">
                    Available Tests
                </a>
                <a href="{{ route('student.subscription') }}" class="quick-link-btn">
                    Manage Subscription
                </a>
                <a href="{{ route('student.settings') }}" class="quick-link-btn">
                    Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Available Tests -->
        <div class="card-section">
            <div class="card-header">
                <h3 style="display: flex; align-items: center; gap: 8px; margin: 0;">
                    <svg style="width: 20px; height: 20px;" data-lucide="file-text"></svg>
                    Available Tests
                </h3>
                <a href="{{ route('student.tests') }}" class="card-header-link">View All</a>
            </div>
            <div class="card-body">
                @forelse($availableTests as $test)
                <div style="padding: 1rem 0; border-bottom: 1px solid var(--color-gray-100); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ $test->title }}</div>
                        <div style="font-size: 0.85rem; color: var(--color-gray-600);">{{ $test->total_questions ?? 0 }} questions • {{ $test->duration_minutes ?? 0 }} min</div>
                    </div>
                    <a href="{{ route('student.tests.show', $test->id) }}" style="padding: 0.5rem 1rem; background-color: var(--color-primary); color: white; text-decoration: none; border-radius: 0.25rem; font-size: 0.85rem;">Start</a>
                </div>
                @empty
                <div style="text-align: center; color: var(--color-gray-400); padding: 2rem;">No tests available</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Test Attempts -->
        <div class="card-section">
            <div class="card-header">
                <h3 style="display: flex; align-items: center; gap: 8px; margin: 0;">
                    <svg style="width: 20px; height: 20px;" data-lucide="clock"></svg>
                    Recent Attempts
                </h3>
                <a href="{{ route('student.test-history') }}" class="card-header-link">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentTests as $test)
                <div class="activity-item">
                    <div class="activity-icon">
                        <svg style="width: 24px; height: 24px;" data-lucide="beaker"></svg>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Test Attempt #{{ $test->id }}</div>
                        <div class="activity-time">{{ \Carbon\Carbon::parse($test->started_at)->format('M d, H:i') }}</div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: var(--color-gray-400); padding: 1rem;">No test attempts yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
