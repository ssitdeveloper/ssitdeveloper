@extends('layouts.student')

@section('title', 'Analytics & Performance')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Analytics & Performance</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Track your learning progress and performance metrics.</p>
    </div>

    <!-- Stats Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-6);">
        <!-- Overall Accuracy Card -->
        <div class="student-card" style="text-align: center; padding: var(--spacing-6);">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Overall Accuracy</p>
            <p style="margin: 0; font-size: 2.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ round($stats['accuracy'], 1) }}%</p>
            <div style="margin-top: var(--spacing-2); width: 100%; height: 4px; background-color: var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden;">
                <div style="height: 100%; background-color: var(--color-success); width: {{ round($stats['accuracy'], 1) }}%;"></div>
            </div>
        </div>

        <!-- Tests Completed Card -->
        <div class="student-card" style="text-align: center; padding: var(--spacing-6);">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Tests Completed</p>
            <p style="margin: 0; font-size: 2.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $stats['total_tests'] }}</p>
            <p style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-500); font-size: var(--font-size-sm);">Mock exams attempted</p>
        </div>

        <!-- Questions Answered Card -->
        <div class="student-card" style="text-align: center; padding: var(--spacing-6);">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Questions Answered</p>
            <p style="margin: 0; font-size: 2.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $stats['total_questions'] }}</p>
            <p style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-500); font-size: var(--font-size-sm);">Total practice questions</p>
        </div>

        <!-- Study Minutes Card -->
        <div class="student-card" style="text-align: center; padding: var(--spacing-6);">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Study Minutes</p>
            <p style="margin: 0; font-size: 2.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $stats['study_minutes'] }}</p>
            <p style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-500); font-size: var(--font-size-sm);">Total time spent</p>
        </div>
    </div>

    <!-- Subject-wise Accuracy Section -->
    <div class="student-card">
        <h2 style="margin: 0 0 var(--spacing-4) 0; color: var(--color-gray-900); font-size: var(--font-size-lg);">Subject-wise Accuracy</h2>

        @if(count($subjectWiseAnalytics) > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-gray-200);">
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Subject</th>
                            <th style="text-align: center; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Attempted</th>
                            <th style="text-align: center; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Correct</th>
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Accuracy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjectWiseAnalytics as $subject)
                            <tr style="border-bottom: 1px solid var(--color-gray-100); transition: background-color var(--transition-fast);">
                                <td style="padding: var(--spacing-3); color: var(--color-gray-900);">{{ $subject['subject'] }}</td>
                                <td style="padding: var(--spacing-3); text-align: center; color: var(--color-gray-700);">{{ $subject['total'] }}</td>
                                <td style="padding: var(--spacing-3); text-align: center; color: var(--color-gray-700);">{{ $subject['correct'] }}</td>
                                <td style="padding: var(--spacing-3);">
                                    <div style="display: flex; align-items: center; gap: var(--spacing-2);">
                                        <div style="flex: 1; height: 6px; background-color: var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden;">
                                            <div style="height: 100%; background-color: var(--color-primary); width: {{ round($subject['accuracy'], 1) }}%; transition: width var(--transition-fast);"></div>
                                        </div>
                                        <span style="min-width: 50px; text-align: right; color: var(--color-gray-700); font-weight: var(--font-weight-medium);">{{ round($subject['accuracy'], 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: var(--spacing-6);">
                <p style="margin: 0; color: var(--color-gray-500);">No performance data available yet. Start practicing to see analytics!</p>
            </div>
        @endif
    </div>
</div>

<style>
.student-card {
    background-color: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-4);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

table tbody tr:hover {
    background-color: var(--color-gray-50);
}
</style>
@endsection
