@extends('layouts.student')

@section('title', 'Test History')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Test History</h1>
        <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total: {{ $stats['total_tests'] ?? 0 }} tests | Passed: {{ $stats['passed'] ?? 0 }} | Avg Score: {{ $stats['avg_score'] ?? 0 }}%</p>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-4);">
        <div class="student-card" style="text-align: center;">
            <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Tests</p>
            <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $stats['total_tests'] ?? 0 }}</p>
        </div>
        <div class="student-card" style="text-align: center;">
            <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Passed</p>
            <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: #10b981;">{{ $stats['passed'] ?? 0 }}</p>
        </div>
        <div class="student-card" style="text-align: center;">
            <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Average Score</p>
            <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: #f59e0b;">{{ $stats['avg_score'] ?? 0 }}%</p>
        </div>
    </div>

    <!-- Test History Table -->
    @if ($attempts->count() > 0)
        <div class="student-card" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                    <tr>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Test Name</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Date</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Score</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempts as $attempt)
                        <tr style="border-bottom: 1px solid var(--color-gray-200);">
                            <td style="padding: var(--spacing-2); color: var(--color-gray-900);">{{ $attempt->test->name }}</td>
                            <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $attempt->created_at->format('M d, Y') }}</td>
                            <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-semibold);">{{ $attempt->marks_obtained }}%</td>
                            <td style="padding: var(--spacing-2);">
                                @if($attempt->status->value === 'passed')
                                    <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #d4edda; color: #155724; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold);">Passed</span>
                                @else
                                    <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #f8d7da; color: #721c24; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold);">Failed</span>
                                @endif
                            </td>
                            <td style="padding: var(--spacing-2);">
                                <a href="{{ route('student.test-history.show', $attempt) }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold);">Review</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
                {{ $attempts->links() }}
            </div>
        @endif
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-4);">
            <p style="color: var(--color-gray-600); margin: 0;">No test attempts yet. Start taking tests to see your history!</p>
        </div>
    @endif
</div>
@endsection
