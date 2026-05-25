@extends('layouts.student')

@section('title', 'Review Answers')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('results.show', $attempt) }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Result Summary</a>
    </div>

    <!-- Test Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $attempt->test->title }} - Answer Review</h1>
        <p class="text-gray-600 mt-2">Score: {{ number_format($attempt->score ?? 0, 1) }}%</p>
    </div>

    <!-- Questions Review -->
    <div class="space-y-6">
        @forelse($attempt->answers()->with('question.chapter.topic.subject', 'selectedOption', 'correctOption')->get() ?? [] as $index => $answer)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $answer->is_correct ? 'border-green-500' : 'border-red-500' }}">
                <!-- Question Header -->
                <div class="mb-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-grow">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $answer->is_correct ? 'bg-green-100' : 'bg-red-100' }}">
                                    <span class="font-bold {{ $answer->is_correct ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $loop->iteration }}
                                    </span>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $answer->is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $answer->is_correct ? '✓ CORRECT' : '✗ INCORRECT' }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-600 mt-2">
                                <strong>{{ $answer->question->chapter?->topic?->subject->name ?? 'N/A' }}</strong> ›
                                {{ $answer->question->chapter?->topic->name ?? 'N/A' }} ›
                                {{ $answer->question->chapter->name ?? 'N/A' }}
                            </p>

                            <p class="text-gray-900 font-medium mt-3">{{ $answer->question->question_text }}</p>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-gray-600">
                                Time spent:
                                @if($answer->time_spent_seconds)
                                    {{ intdiv($answer->time_spent_seconds, 60) }}m {{ $answer->time_spent_seconds % 60 }}s
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2 my-4">
                    @foreach(['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $label => $field)
                        @php
                            $isSelected = $answer->selectedOption && $answer->selectedOption->option === $label;
                            $isCorrect = $answer->correctOption && $answer->correctOption->option === $label;
                        @endphp
                        <div class="p-3 rounded border-l-4 {{
                            $isSelected && $isCorrect
                                ? 'border-green-500 bg-green-50'
                                : ($isSelected && !$isCorrect
                                    ? 'border-red-500 bg-red-50'
                                    : ($isCorrect
                                        ? 'border-green-500 bg-green-50'
                                        : 'border-gray-300 bg-gray-50'
                                    )
                                )
                        }}">
                            <div class="flex items-start gap-3">
                                <span class="font-bold text-gray-700 min-w-fit">{{ $label }}.</span>
                                <span class="text-gray-900">{{ $answer->question->$field }}</span>

                                <div class="ml-auto flex items-center gap-2">
                                    @if($isSelected && $isCorrect)
                                        <span class="text-green-600 font-bold text-xs">✓ Your answer</span>
                                    @elseif($isSelected)
                                        <span class="text-red-600 font-bold text-xs">✗ Your answer</span>
                                    @endif

                                    @if($isCorrect && !$isSelected)
                                        <span class="text-green-600 font-bold text-xs">✓ Correct answer</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Explanation (if available) -->
                @if($answer->question->explanation)
                <div class="bg-blue-50 border border-blue-200 rounded p-4 mt-4">
                    <h4 class="font-semibold text-blue-900 text-sm mb-2">Explanation:</h4>
                    <p class="text-sm text-blue-800">{{ $answer->question->explanation }}</p>
                </div>
                @endif

                <!-- Learning Resources (if available) -->
                @if($answer->question->learning_video_url || $answer->question->learning_material)
                <div class="bg-purple-50 border border-purple-200 rounded p-4 mt-4">
                    <h4 class="font-semibold text-purple-900 text-sm mb-2">Learn More:</h4>
                    <div class="space-y-2">
                        @if($answer->question->learning_video_url)
                            <a href="{{ $answer->question->learning_video_url }}" target="_blank" class="block text-sm text-purple-600 hover:text-purple-800">
                                📹 Watch Video Explanation →
                            </a>
                        @endif
                        @if($answer->question->learning_material)
                            <p class="text-sm text-purple-800">{{ $answer->question->learning_material }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600 text-lg">No answers to review</p>
            </div>
        @endforelse
    </div>

    <!-- Navigation -->
    <div class="mt-8 flex justify-between">
        <a href="{{ route('results.show', $attempt) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Result Summary
        </a>
        <a href="{{ route('results.recommendations', $attempt) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            View Recommendations →
        </a>
    </div>
</div>
@endsection
