@extends('layouts.admin')

@section('title', 'Attempt Details')

@section('content')
<div class="admin-content">
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('admin.results.attempts') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Attempts</a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $attempt->test->title }}</h1>
                <p class="text-gray-600 mt-2">
                    <strong>Student:</strong> {{ $attempt->user->name }} ({{ $attempt->user->email }})
                </p>
                <p class="text-gray-600">
                    <strong>Date:</strong> {{ $attempt->created_at->format('F d, Y H:i') }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold {{ $attempt->score >= 75 ? 'text-green-600' : ($attempt->score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ number_format($attempt->score ?? 0, 1) }}%
                </div>
                <p class="text-gray-600 mt-1">Score</p>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-gray-600 text-sm">Total Questions</p>
            <p class="text-2xl font-bold text-gray-900">{{ count(json_decode($attempt->question_ids, true) ?? []) }}</p>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-gray-600 text-sm">Correct Answers</p>
            <p class="text-2xl font-bold text-green-600">{{ $attempt->answers()->where('is_correct', true)->count() }}</p>
        </div>

        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <p class="text-gray-600 text-sm">Incorrect Answers</p>
            <p class="text-2xl font-bold text-red-600">{{ $attempt->answers()->where('is_correct', false)->count() }}</p>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <p class="text-gray-600 text-sm">Time Taken</p>
            <p class="text-2xl font-bold text-gray-900">
                @if($attempt->started_at && $attempt->completed_at)
                    {{ $attempt->completed_at->diffInMinutes($attempt->started_at) }} min
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>

    <!-- Subject-wise Breakdown -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subject-wise Accuracy</h2>
        <div class="space-y-3">
            @forelse($subjectAccuracy ?? [] as $subject => $data)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $subject }}</span>
                        <span class="text-sm font-semibold">{{ round($data['accuracy']) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data['accuracy'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $data['correct'] }} of {{ $data['total'] }} questions correct</p>
                </div>
            @empty
                <p class="text-gray-600">No subject data available</p>
            @endforelse
        </div>
    </div>

    <!-- Chapter-wise Breakdown -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Chapter-wise Accuracy</h2>
        <div class="space-y-3">
            @forelse($chapterAccuracy ?? [] as $chapter => $data)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $chapter }}</span>
                        <span class="text-sm font-semibold">{{ round($data['accuracy']) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $data['accuracy'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $data['correct'] }} of {{ $data['total'] }} questions correct</p>
                </div>
            @empty
                <p class="text-gray-600">No chapter data available</p>
            @endforelse
        </div>
    </div>

    <!-- Questions Review -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Question-by-Question Review</h2>

        @forelse($attempt->answers()->with('question.chapter.topic.subject', 'selectedOption', 'correctOption')->get() ?? [] as $index => $answer)
            <div class="mb-6 pb-6 border-b" @if($loop->last) class="mb-0 pb-0 border-b-0" @endif>
                <div class="flex items-start gap-4">
                    <!-- Question Number & Status -->
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full {{ $answer->is_correct ? 'bg-green-100' : 'bg-red-100' }}">
                            <span class="text-lg font-semibold {{ $answer->is_correct ? 'text-green-600' : 'text-red-600' }}">
                                {{ $loop->iteration }}
                            </span>
                        </div>
                    </div>

                    <!-- Question Details -->
                    <div class="flex-grow">
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">
                                <strong>{{ $answer->question->chapter?->topic?->subject->name ?? 'N/A' }}</strong> ›
                                {{ $answer->question->chapter?->topic->name ?? 'N/A' }} ›
                                {{ $answer->question->chapter->name ?? 'N/A' }}
                            </p>
                            <p class="text-gray-900 font-medium">Q{{ $loop->iteration }}: {{ $answer->question->question_text }}</p>
                        </div>

                        <!-- Options -->
                        <div class="space-y-2 mb-4">
                            @foreach(['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $label => $field)
                                <div class="px-3 py-2 rounded border-l-4 {{
                                    ($answer->selectedOption && $answer->selectedOption->option === $label)
                                        ? ($answer->is_correct ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50')
                                        : (($answer->correctOption && $answer->correctOption->option === $label)
                                            ? 'border-green-500 bg-green-50'
                                            : 'border-gray-300 bg-gray-50')
                                }}">
                                    <p class="font-medium">{{ $label }}. {{ $answer->question->$field }}</p>
                                    <div class="text-xs mt-1">
                                        @if($answer->selectedOption && $answer->selectedOption->option === $label)
                                            <span class="text-blue-600 font-medium">← Student's Answer</span>
                                        @endif
                                        @if($answer->correctOption && $answer->correctOption->option === $label)
                                            <span class="text-green-600 font-medium">✓ Correct</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Status Badge -->
                        <div class="inline-block">
                            @if($answer->is_correct)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">✓ Correct</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">✗ Incorrect</span>
                            @endif
                        </div>

                        <!-- Time Spent -->
                        <p class="text-xs text-gray-600 mt-3">
                            <strong>Time spent:</strong>
                            @if($answer->time_spent_seconds)
                                {{ intdiv($answer->time_spent_seconds, 60) }}m {{ $answer->time_spent_seconds % 60 }}s
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600">No answers found for this attempt</p>
        @endforelse
    </div>
</div>
@endsection
