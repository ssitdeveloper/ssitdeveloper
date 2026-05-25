@extends('layouts.admin')

@section('title', 'Results Dashboard')

@section('content')
<div class="admin-content">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Results & Analytics</h1>
        <p class="text-gray-600 mt-2">Monitor student performance and test statistics</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Unique Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['unique_students'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Total Attempts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_attempts'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_attempts'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Avg Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_score'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Attempts Trend (Last 30 Days) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Attempts Trend (Last 30 Days)</h2>
            <canvas id="attemptsChart" class="w-full" height="300"></canvas>
        </div>

        <!-- Score Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Score Distribution</h2>
            <canvas id="scoreChart" class="w-full" height="300"></canvas>
        </div>
    </div>

    <!-- Top Tests -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Most Popular Tests</h2>
            <ul class="space-y-3">
                @forelse($stats['top_tests'] ?? [] as $test)
                    <li class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-700">{{ $test['name'] ?? 'Test' }}</span>
                        <span class="text-sm text-gray-600">{{ $test['attempts'] ?? 0 }} attempts</span>
                    </li>
                @empty
                    <li class="text-gray-600 text-sm">No test data available</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance Summary</h2>
            <div class="space-y-4">
                @forelse($stats['score_ranges'] ?? [] as $range => $count)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $range }}</span>
                            <span class="text-sm text-gray-600">{{ $count }} students</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($count / max(1, $stats['unique_students'] ?? 1)) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 text-sm">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Action Links -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.results.attempts') }}" class="p-4 border-2 border-blue-200 rounded-lg hover:border-blue-600 hover:bg-blue-50 transition">
                <h3 class="font-medium text-gray-900">View All Attempts</h3>
                <p class="text-sm text-gray-600 mt-1">See detailed attempt history and filters</p>
            </a>

            <a href="{{ route('admin.results.problematic-questions') }}" class="p-4 border-2 border-red-200 rounded-lg hover:border-red-600 hover:bg-red-50 transition">
                <h3 class="font-medium text-gray-900">Problematic Questions</h3>
                <p class="text-sm text-gray-600 mt-1">Find questions with low accuracy</p>
            </a>

            <form action="{{ route('admin.results.attempts.export') }}" method="POST" class="p-4 border-2 border-green-200 rounded-lg hover:border-green-600 hover:bg-green-50 transition">
                @csrf
                <button type="submit" class="w-full text-left">
                    <h3 class="font-medium text-gray-900">Export Results</h3>
                    <p class="text-sm text-gray-600 mt-1">Download attempts as CSV</p>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attempts Chart
    const attemptsCtx = document.getElementById('attemptsChart').getContext('2d');
    new Chart(attemptsCtx, {
        type: 'line',
        data: {
            labels: @json($stats['attempts_by_day']?->pluck('date') ?? []),
            datasets: [{
                label: 'Attempts',
                data: @json($stats['attempts_by_day']?->pluck('count') ?? []),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Score Distribution Chart
    const scoreCtx = document.getElementById('scoreChart').getContext('2d');
    new Chart(scoreCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($stats['score_ranges'] ?? [])),
            datasets: [{
                data: @json(array_values($stats['score_ranges'] ?? [])),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
