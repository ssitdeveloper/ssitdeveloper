@extends('layouts.student')

@section('title', 'Leaderboard')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Top Performers</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">See how you rank against other students.</p>
    </div>

    <div class="student-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-gray-200); background-color: var(--color-gray-50);">
                        <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Rank</th>
                        <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Student Name</th>
                        <th style="text-align: center; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Accuracy</th>
                        <th style="text-align: center; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Tests Taken</th>
                        <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaderboard as $index => $user)
                        <tr style="border-bottom: 1px solid var(--color-gray-100); @if($index < 3) background-color: var(--color-gray-50); @endif transition: background-color var(--transition-fast);">
                            <td style="padding: var(--spacing-3); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">
                                @if ($index === 0)
                                    <span style="font-size: 1.5rem; color: #FFD700;">🥇 1st</span>
                                @elseif ($index === 1)
                                    <span style="font-size: 1.5rem; color: #C0C0C0;">🥈 2nd</span>
                                @elseif ($index === 2)
                                    <span style="font-size: 1.5rem; color: #CD7F32;">🥉 3rd</span>
                                @else
                                    <span style="color: var(--color-gray-600);">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td style="padding: var(--spacing-3); color: var(--color-gray-900);">
                                <div style="display: flex; align-items: center; gap: var(--spacing-2);">
                                    <img src="{{ $user['user']['avatar'] ?? 'https://via.placeholder.com/40' }}" alt="{{ $user['user']['name'] }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                    <span style="font-weight: var(--font-weight-medium);">{{ $user['user']['name'] }}</span>
                                </div>
                            </td>
                            <td style="padding: var(--spacing-3); text-align: center; color: var(--color-primary); font-weight: var(--font-weight-medium);">{{ round($user['accuracy_percentage'], 1) }}%</td>
                            <td style="padding: var(--spacing-3); text-align: center; color: var(--color-gray-700);">{{ $user['total_tests_taken'] }}</td>
                            <td style="padding: var(--spacing-3);">
                                <div style="display: flex; align-items: center; gap: var(--spacing-2);">
                                    <div style="flex: 1; height: 6px; background-color: var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden;">
                                        <div style="height: 100%; background-color: var(--color-success); width: {{ min($user['accuracy_percentage'], 100) }}%; transition: width var(--transition-fast);"></div>
                                    </div>
                                    <span style="min-width: 40px; text-align: right; font-size: var(--font-size-sm); color: var(--color-gray-600);">{{ round($user['accuracy_percentage'], 0) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: var(--spacing-6); text-align: center; color: var(--color-gray-500);">
                                No leaderboard data available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
