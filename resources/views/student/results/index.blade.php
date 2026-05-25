@extends('layouts.student')

@section('title', 'My Test Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Test Results</h1>
        <p class="text-gray-600 mt-2">Review your past test attempts and performance</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('results.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div>
                <label for="score_min" class="block text-sm font-medium text-gray-700">Min Score (%)</label>
                <input type="number" name="score_min" id="score_min" min="0" max="100" value="{{ request('score_min') }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Filter
                </button>
                <a href="{{ route('results.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-gray-600 text-sm">Tests Attempted</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_attempts'] ?? 0 }}</p>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-gray-600 text-sm">Avg Score</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['avg_score'] ?? 0, 1) }}%</p>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <p class="text-gray-600 text-sm">Best Score</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['best_score'] ?? 0, 1) }}%</p>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <p class="text-gray-600 text-sm">Worst Score</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['worst_score'] ?? 0, 1) }}%</p>
        </div>
    </div>

    <!-- Results List -->
    <div class="space-y-4">
        @forelse($attempts ?? [] as $attempt)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="flex justify-between items-start">
                    <div class="flex-grow">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $attempt->test->title }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $attempt->created_at->format('F d, Y - H:i A') }}</p>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <p class="text-gray-600 text-xs">Questions Attempted</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $attempt->answers()->count() }} / {{ count(json_decode($attempt->question_ids, true) ?? []) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-xs">Time Taken</p>
                                <p class="font-semibold text-gray-900">
                                    @if($attempt->started_at && $attempt->completed_at)
                                        {{ $attempt->completed_at->diffInMinutes($attempt->started_at) }} min
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-xs">Status</p>
                                <p class="font-semibold capitalize">
                                    <span class="px-2 py-1 rounded text-xs {{
                                        $attempt->status === 'completed' ? 'bg-green-100 text-green-800' :
                                        ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                    }}">
                                        {{ str_replace('_', ' ', $attempt->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Score Display -->
                    <div class="text-right">
                        <div class="text-4xl font-bold {{ $attempt->score >= 75 ? 'text-green-600' : ($attempt->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($attempt->score ?? 0, 1) }}%
                        </div>
                        <p class="text-gray-600 text-sm mt-2">
                            @if($attempt->score >= 75)
                                Excellent!
                            @elseif($attempt->score >= 50)
                                Good effort
                            @else
                                Keep practicing
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-4 pt-4 border-t">
                    <a href="{{ route('results.show', $attempt) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        View Detailed Results
                    </a>
                    <a href="{{ route('results.review', $attempt) }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">
                        Review Answers
                    </a>
                    <a href="{{ route('results.export-pdf', $attempt) }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">
                        Download PDF
                    </a>
                    @if($attempt->score < 75)
                    <a href="{{ route('results.recommendations', $attempt) }}" class="text-green-600 hover:text-green-800 font-medium text-sm">
                        Get Recommendations
                    </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600 text-lg">No test results yet</p>
                <p class="text-gray-500 text-sm mt-2">Start taking tests to see your performance history</p>
                <a href="{{ route('student.my_tests') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">
                    Browse Tests →
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($attempts && method_exists($attempts, 'links'))
    <div class="mt-8">
        {{ $attempts->links() }}
    </div>
    @endif
</div>
@endsection
