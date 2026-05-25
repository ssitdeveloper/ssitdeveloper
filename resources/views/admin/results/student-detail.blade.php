@extends('layouts.admin')

@section('title', 'Student Performance')

@section('content')
<div class="admin-content">
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('admin.results.attempts') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Attempts</a>
    </div>

    <!-- Student Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $student->name }}</h1>
                <p class="text-gray-600 mt-2">{{ $student->email }}</p>
                <p class="text-gray-600">
                    Joined: {{ $student->created_at->format('F d, Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-gray-600 text-sm">Total Attempts</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_attempts'] ?? 0 }}</p>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-gray-600 text-sm">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['completed_attempts'] ?? 0 }}</p>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <p class="text-gray-600 text-sm">Avg Score</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['avg_score'] ?? 0, 1) }}%</p>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <p class="text-gray-600 text-sm">Best Score</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['best_score'] ?? 0, 1) }}%</p>
        </div>
    </div>

    <!-- Score Trend Chart -->
    @if($scoreTrend && count($scoreTrend) > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Score Trend</h2>
        <canvas id="scoreChart" class="w-full" height="300"></canvas>
    </div>
    @endif

    <!-- Attempts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-900">All Attempts</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Test Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Score</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Q. Answered</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Duration</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($attempts ?? [] as $attempt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                {{ $attempt->test->title }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="font-semibold {{ $attempt->score >= 75 ? 'text-green-600' : ($attempt->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($attempt->score ?? 0, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $attempt->answers()->count() }} / {{ count(json_decode($attempt->question_ids, true) ?? []) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($attempt->started_at && $attempt->completed_at)
                                    {{ $attempt->completed_at->diffInMinutes($attempt->started_at) }} min
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{
                                    $attempt->status === 'completed' ? 'bg-green-100 text-green-800' :
                                    ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $attempt->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.results.attempt', $attempt) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-600">
                                No test attempts yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white border-t px-6 py-4">
            {{ $attempts->links() ?? '' }}
        </div>
    </div>
</div>

@if($scoreTrend && count($scoreTrend) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('scoreChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($scoreTrend->pluck('date') ?? []),
            datasets: [{
                label: 'Test Scores',
                data: @json($scoreTrend->pluck('score') ?? []),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: function(v) { return v + '%'; } }
                }
            }
        }
    });
</script>
@endif
@endsection
