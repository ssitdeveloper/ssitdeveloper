@extends('layouts.admin')

@section('title', 'View Test')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('admin.tests.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Tests</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.tests.edit', $test) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block;">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.tests.destroy', $test) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this test?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-danger); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm); margin-bottom: var(--spacing-4);">
        <!-- Test Header -->
        <div style="margin-bottom: var(--spacing-4); padding-bottom: var(--spacing-3); border-bottom: 1px solid var(--color-gray-200);">
            <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">{{ $test->name }}</h1>
            <p style="margin: 0; color: var(--color-gray-600); line-height: 1.6;">{{ $test->description }}</p>
        </div>

        <!-- Test Details Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-4); margin-bottom: var(--spacing-4);">
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Questions</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $test->total_questions }}</p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Duration</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $test->duration_minutes }} min</p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Passing Score</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $test->passing_score }}%</p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Attempts</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $test->attempts->count() }}</p>
            </div>
        </div>

        <!-- Test Statistics -->
        <div style="margin-bottom: var(--spacing-4); padding: var(--spacing-3); background-color: rgba(59, 130, 246, 0.1); border-radius: var(--radius-lg); border-left: 4px solid #3b82f6;">
            <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Performance Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--spacing-3);">
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Average Score</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: #3b82f6;">
                        @if($test->attempts->count() > 0)
                            {{ round($test->attempts->avg('marks_obtained')) }}%
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Highest Score</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: #10b981;">
                        @if($test->attempts->count() > 0)
                            {{ $test->attempts->max('marks_obtained') }}%
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Pass Rate</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: #f59e0b;">
                        @if($test->attempts->count() > 0)
                            {{ round(($test->attempts->where('status', 'passed')->count() / $test->attempts->count()) * 100) }}%
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attempts Table -->
    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h2 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-gray-900);">Recent Attempts</h2>

        @if($test->attempts->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                        <tr>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Student</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Date</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Score</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Time Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($test->attempts->take(10) as $attempt)
                            <tr style="border-bottom: 1px solid var(--color-gray-200);">
                                <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">{{ $attempt->user->name }}</td>
                                <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $attempt->created_at->format('M d, Y') }}</td>
                                <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-semibold);">{{ $attempt->marks_obtained }}%</td>
                                <td style="padding: var(--spacing-2);">
                                    @if($attempt->status->value === 'passed')
                                        <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #d4edda; color: #155724; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Passed</span>
                                    @else
                                        <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #f8d7da; color: #721c24; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Failed</span>
                                    @endif
                                </td>
                                <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $attempt->time_taken_minutes ?? 'N/A' }} min</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--color-gray-600); text-align: center; padding: var(--spacing-4);">No attempts yet for this test.</p>
        @endif
    </div>
</div>
@endsection
