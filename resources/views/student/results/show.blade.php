@extends('layouts.student')

@section('title', 'Test Result Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('results.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Results</a>
    </div>

    <!-- Test Info Card -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $attempt->test->title }}</h1>
                <p class="text-gray-600 mt-2">{{ $attempt->created_at->format('F d, Y - H:i A') }}</p>
            </div>
            <div class="text-center">
                <div class="text-5xl font-bold {{ $attempt->score >= 75 ? 'text-green-600' : ($attempt->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ number_format($attempt->score ?? 0, 1) }}%
                </div>
                <p class="text-gray-600 text-sm mt-2">Your Score</p>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-8 pt-8 border-t">
            <div>
                <p class="text-gray-600 text-sm">Total Questions</p>
                <p class="text-2xl font-bold text-gray-900">{{ count(json_decode($attempt->question_ids, true) ?? []) }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Attempted</p>
                <p class="text-2xl font-bold text-blue-600">{{ $attempt->answers()->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Correct</p>
                <p class="text-2xl font-bold text-green-600">{{ $attempt->answers()->where('is_correct', true)->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Incorrect</p>
                <p class="text-2xl font-bold text-red-600">{{ $attempt->answers()->where('is_correct', false)->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Time Taken</p>
                <p class="text-2xl font-bold text-purple-600">
                    @if($attempt->started_at && $attempt->completed_at)
                        {{ $attempt->completed_at->diffInMinutes($attempt->started_at) }} min
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Subject-wise Accuracy -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subject-wise Performance</h2>
        <div class="space-y-4">
            @forelse($subjectAccuracy ?? [] as $subject => $data)
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $subject }}</span>
                        <span class="text-sm font-bold {{ $data['accuracy'] >= 75 ? 'text-green-600' : ($data['accuracy'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ round($data['accuracy']) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full {{ $data['accuracy'] >= 75 ? 'bg-green-600' : ($data['accuracy'] >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ $data['accuracy'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $data['correct'] }} of {{ $data['total'] }} correct</p>
                </div>
            @empty
                <p class="text-gray-600">No subject data available</p>
            @endforelse
        </div>
    </div>

    <!-- Chapter-wise Accuracy -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Chapter-wise Performance</h2>
        <div class="space-y-4">
            @forelse($chapterAccuracy ?? [] as $chapter => $data)
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $chapter }}</span>
                        <span class="text-sm font-bold {{ $data['accuracy'] >= 75 ? 'text-green-600' : ($data['accuracy'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ round($data['accuracy']) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $data['accuracy'] >= 75 ? 'bg-green-600' : ($data['accuracy'] >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ $data['accuracy'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $data['correct'] }} of {{ $data['total'] }} correct</p>
                </div>
            @empty
                <p class="text-gray-600">No chapter data available</p>
            @endforelse
        </div>
    </div>

    <!-- Class Comparison -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">How You Compare</h2>
        <div class="grid grid-cols-2 gap-6">
            <div class="text-center p-4 border rounded-lg">
                <p class="text-gray-600 text-sm">Your Score</p>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($attempt->score ?? 0, 1) }}%</p>
            </div>
            <div class="text-center p-4 border rounded-lg">
                <p class="text-gray-600 text-sm">Class Average</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($classAverage ?? 0, 1) }}%</p>
            </div>
        </div>
        @if($attempt->score > ($classAverage ?? 0))
            <p class="text-green-600 font-medium mt-4">✓ You're performing above class average!</p>
        @else
            <p class="text-yellow-600 font-medium mt-4">⚠ You're performing below class average. Review weak areas and practice more.</p>
        @endif
    </div>

    <!-- Action Links -->
    <div class="flex gap-3 mb-8">
        <a href="{{ route('results.review', $attempt) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
            Review Answers
        </a>
        <a href="{{ route('results.recommendations', $attempt) }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium">
            Get Recommendations
        </a>
        <a href="{{ route('results.export-pdf', $attempt) }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 font-medium">
            Download Report
        </a>
    </div>

    <!-- Next Steps -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Recommended Next Steps</h3>
        <ul class="space-y-2 text-sm text-gray-700">
            <li>✓ Review your weak topics from the chapters above</li>
            <li>✓ Check the "Review Answers" section to understand mistakes</li>
            <li>✓ Get personalized practice questions on weak areas</li>
            <li>✓ Take another test after 2-3 days to track improvement</li>
        </ul>
    </div>
</div>
@endsection
