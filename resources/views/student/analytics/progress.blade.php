@extends('layouts.student')

@section('title', 'Learning Progress')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Learning Progress</h1>
        <p style="margin: 0; color: var(--color-gray-600);">Track your progress across chapters</p>
    </div>

    <!-- Progress Stats -->
    @if(isset($stats))
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-4);">
            <div class="student-card" style="text-align: center;">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Chapters Started</p>
                <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $stats['chapters_started'] ?? 0 }}</p>
            </div>
            <div class="student-card" style="text-align: center;">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Chapters Completed</p>
                <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: #10b981;">{{ $stats['chapters_completed'] ?? 0 }}</p>
            </div>
            <div class="student-card" style="text-align: center;">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Avg Progress</p>
                <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: #f59e0b;">{{ $stats['avg_progress'] ?? 0 }}%</p>
            </div>
        </div>
    @endif

    <!-- Progress Table -->
    @if ($learningProgress->count() > 0)
        <div class="student-card" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                    <tr>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Chapter</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Topic</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Progress</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($learningProgress as $progress)
                        <tr style="border-bottom: 1px solid var(--color-gray-200);">
                            <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">{{ $progress->chapter->name }}</td>
                            <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $progress->chapter->topic->name }}</td>
                            <td style="padding: var(--spacing-2);">
                                <div style="background-color: var(--color-gray-200); height: 24px; border-radius: var(--radius-lg); overflow: hidden; display: flex; align-items: center; width: 150px;">
                                    <div style="background: linear-gradient(90deg, var(--color-primary), #3b82f6); height: 100%; width: {{ $progress->progress }}%; transition: width 0.3s ease;"></div>
                                </div>
                                <p style="margin: var(--spacing-1) 0 0 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">{{ $progress->progress }}%</p>
                            </td>
                            <td style="padding: var(--spacing-2); color: var(--color-gray-700); font-size: var(--font-size-sm);">{{ $progress->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($learningProgress->hasPages())
            <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
                {{ $learningProgress->links() }}
            </div>
        @endif
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-4);">
            <p style="color: var(--color-gray-600); margin: 0;">Start learning to see your progress!</p>
        </div>
    @endif
</div>
@endsection
