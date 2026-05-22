@extends('layouts.student')

@section('title', 'Test History')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Test History</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Review all your test attempts and performance</p>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-4);">
        <div class="student-card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--color-primary); font-weight: var(--font-weight-bold);">{{ $stats['total_tests'] }}</div>
            <div style="color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Attempts</div>
        </div>
        <div class="student-card" style="text-align: center;">
            <div style="font-size: 2rem; color: #22c55e; font-weight: var(--font-weight-bold);">{{ $stats['passed'] }}</div>
            <div style="color: var(--color-gray-600); font-size: var(--font-size-sm);">Tests Passed</div>
        </div>
        <div class="student-card" style="text-align: center;">
            <div style="font-size: 2rem; color: #f59e0b; font-weight: var(--font-weight-bold);">{{ $stats['avg_score'] }}%</div>
            <div style="color: var(--color-gray-600); font-size: var(--font-size-sm);">Average Score</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="student-card" style="margin-bottom: var(--spacing-4);">
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-2);">
            <div>
                <label style="display: block; font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); margin-bottom: var(--spacing-1); color: var(--color-gray-700);">Status</label>
                <select name="status" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
                    <option value="">All</option>
                    <option value="passed" @if(request('status') == 'passed') selected @endif>Passed</option>
                    <option value="failed" @if(request('status') == 'failed') selected @endif>Failed</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); margin-bottom: var(--spacing-1); color: var(--color-gray-700);">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
            </div>
            <div>
                <label style="display: block; font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); margin-bottom: var(--spacing-1); color: var(--color-gray-700);">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" style="width: 100%; padding: var(--spacing-2); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Filter</button>
            </div>
        </form>
    </div>

    <!-- Test Attempts Table -->
    <div class="student-card" style="overflow-x: auto;">
        @if($attempts->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                    <tr>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Test Name</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Date</th>
                        <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Score</th>
                        <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                        <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Time Spent</th>
                        <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attempts as $attempt)
                        <tr style="border-bottom: 1px solid var(--color-gray-200);">
                            <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">{{ $attempt->test->title ?? 'Test' }}</td>
                            <td style="padding: var(--spacing-2); color: var(--color-gray-700); font-size: var(--font-size-sm);">{{ $attempt->created_at->format('M d, Y') }}</td>
                            <td style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold);">
                                <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color:
                                    @if(($attempt->marks_obtained / $attempt->test->total_marks ?? 0) * 100 >= 60) #d4edda @else #f8d7da @endif;
                                    color: @if(($attempt->marks_obtained / $attempt->test->total_marks ?? 0) * 100 >= 60) #155724 @else #721c24 @endif;
                                    border-radius: var(--radius-lg);">
                                    {{ round(($attempt->marks_obtained / ($attempt->test->total_marks ?? 1)) * 100, 1) }}%
                                </span>
                            </td>
                            <td style="padding: var(--spacing-2); text-align: center;">
                                <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color:
                                    @if($attempt->status === 'passed') #d4edda @elseif($attempt->status === 'failed') #f8d7da @else #e2e3e5 @endif;
                                    color: @if($attempt->status === 'passed') #155724 @elseif($attempt->status === 'failed') #721c24 @else #383d41 @endif;
                                    border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold);">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            </td>
                            <td style="padding: var(--spacing-2); text-align: center; color: var(--color-gray-700); font-size: var(--font-size-sm);">
                                {{ $attempt->time_spent_minutes ?? 0 }} min
                            </td>
                            <td style="padding: var(--spacing-2); text-align: center;">
                                <a href="{{ route('student.test-history.show', $attempt) }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium);">View Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($attempts->hasPages())
                <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
                    {{ $attempts->links() }}
                </div>
            @endif
        @else
            <div style="text-align: center; padding: var(--spacing-4); color: var(--color-gray-500);">
                <p style="margin: 0;">No test attempts found. Start taking tests to see your history here!</p>
            </div>
        @endif
    </div>
</div>
@endsection
