@extends('layouts.admin')

@section('title', 'Problematic Questions')

@section('content')
<div class="admin-content">
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('admin.results.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Results Dashboard</a>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Problematic Questions</h1>
        <p class="text-gray-600 mt-2">Questions with the lowest accuracy rates across all students</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.results.problematic-questions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                <select name="subject_id" id="subject_id" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
                    <option value="">All Subjects</option>
                    @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="min_accuracy" class="block text-sm font-medium text-gray-700">Max Accuracy (%)</label>
                <input type="number" name="min_accuracy" id="min_accuracy" min="0" max="100" value="{{ request('min_accuracy', 50) }}" class="mt-1 block w-full rounded-md border-gray-300 border px-3 py-2">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.results.problematic-questions') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Questions List -->
    <div class="space-y-4">
        @forelse($questions ?? [] as $index => $question)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Q{{ $index + 1 }}: {{ Str::limit($question->question_text, 100) }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>{{ $question->chapter?->topic?->subject->name ?? 'N/A' }}</strong> ›
                                {{ $question->chapter?->topic->name ?? 'N/A' }} ›
                                {{ $question->chapter->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-red-600">{{ $question->accuracy ?? 0 }}%</p>
                            <p class="text-sm text-gray-600">Accuracy</p>
                        </div>
                    </div>
                </div>

                <!-- Performance Stats -->
                <div class="grid grid-cols-4 gap-4 mb-4 py-3 border-y">
                    <div>
                        <p class="text-sm text-gray-600">Total Attempts</p>
                        <p class="text-lg font-bold text-gray-900">{{ $question->total_attempts ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Correct</p>
                        <p class="text-lg font-bold text-green-600">{{ $question->correct_answers ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Incorrect</p>
                        <p class="text-lg font-bold text-red-600">{{ ($question->total_attempts ?? 0) - ($question->correct_answers ?? 0) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Difficulty</p>
                        <p class="text-lg font-bold capitalize">{{ $question->difficulty ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Most Common Incorrect Answer -->
                @if($question->most_common_incorrect)
                <div class="bg-yellow-50 rounded p-4 mb-4">
                    <p class="text-sm font-medium text-gray-900 mb-2">Most Common Incorrect Answer:</p>
                    <p class="text-sm text-gray-700">
                        @php
                            $optionMap = ['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'];
                            $option = $optionMap[$question->most_common_incorrect] ?? '';
                        @endphp
                        <strong>{{ $question->most_common_incorrect }}.</strong> {{ $question->$option ?? 'N/A' }}
                    </p>
                </div>
                @endif

                <!-- Options Display -->
                <div class="space-y-2 mb-4">
                    <p class="text-sm font-medium text-gray-900 mb-2">All Options:</p>
                    @foreach(['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $label => $field)
                        <div class="px-3 py-2 rounded border-l-4 {{
                            $question->correct_option === $label
                                ? 'border-green-500 bg-green-50'
                                : 'border-gray-300 bg-gray-50'
                        }}">
                            <p class="text-sm">
                                <strong>{{ $label }}.</strong> {{ $question->$field }}
                                @if($question->correct_option === $label)
                                    <span class="ml-2 text-green-600 font-medium">✓ Correct</span>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.questions.edit', $question) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Edit Question
                    </a>
                    <a href="#" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                        View Attempts
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600 text-lg">No problematic questions found</p>
                <p class="text-gray-500 text-sm mt-2">Either all questions are performing well or filters need adjustment</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($questions && method_exists($questions, 'links'))
    <div class="mt-8">
        {{ $questions->links() }}
    </div>
    @endif
</div>
@endsection
