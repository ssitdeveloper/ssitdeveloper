@extends('layouts.app')

@section('title', 'Question Practice')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('question-bank.index') }}" class="text-blue-600 hover:text-blue-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Question Bank
                    </a>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm text-gray-600">
                        {{ $question->chapter->topic->subject->name }} >
                        {{ $question->chapter->topic->name }} >
                        {{ $question->chapter->name }}
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($question->difficulty_level === 'easy') bg-green-100 text-green-800
                        @elseif($question->difficulty_level === 'medium') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($question->difficulty_level->value) }}
                    </span>
                    <form method="POST" action="{{ route('question-bank.toggle-bookmark', $question->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:text-yellow-700">
                            <i class="fas fa-bookmark {{ $isBookmarked ? 'text-yellow-500' : '' }}"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Question Card -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Question</h1>
                <div class="prose prose-lg max-w-none">
                    {!! $question->question_text !!}
                </div>
            </div>

            <!-- Options -->
            <form method="POST" action="{{ route('question-bank.validate-answer', $question->id) }}" id="answer-form">
                @csrf
                <div class="space-y-3 mb-6">
                    @foreach($question->options as $option)
                        <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="selected_option_id" value="{{ $option->id }}"
                                   class="mt-1 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <span class="text-gray-900">{!! $option->option_text !!}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <span><i class="fas fa-eye mr-1"></i>{{ $question->views_count }} views</span>
                        <span class="ml-4"><i class="fas fa-check-circle mr-1"></i>{{ $question->attempts_count }} attempts</span>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Submit Answer
                    </button>
                </div>
            </form>
        </div>

        <!-- Answer Result (if submitted) -->
        @if(session('validation_result'))
            <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        @if(session('validation_result')['is_correct'])
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-xl"></i>
                            </div>
                        @else
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-red-600 text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            {{ session('validation_result')['is_correct'] ? 'Correct Answer!' : 'Incorrect Answer' }}
                        </h3>
                        <p class="text-gray-600 mb-4">{{ session('validation_result')['message'] }}</p>

                        @if(session('validation_result')['explanation'])
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Explanation:</h4>
                                <div class="text-blue-800">
                                    {!! session('validation_result')['explanation'] !!}
                                </div>
                            </div>
                        @endif

                        @if(!session('validation_result')['is_correct'] && session('validation_result')['correct_option'])
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h4 class="text-sm font-medium text-green-900 mb-2">Correct Answer:</h4>
                                <div class="text-green-800">
                                    {!! session('validation_result')['correct_option']['option_text'] !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('question-bank.show', $question->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Try Again
                    </a>
                    <a href="{{ route('question-bank.chapter', $question->chapter_id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        More from this chapter
                    </a>
                    <a href="{{ route('question-bank.practice') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Practice Mode
                    </a>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    @if($question->chapter->questions()->where('id', '<', $question->id)->exists())
                        <a href="{{ route('question-bank.show', $question->chapter->questions()->where('id', '<', $question->id)->orderBy('id', 'desc')->first()->id) }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            <i class="fas fa-chevron-left mr-1"></i>Previous Question
                        </a>
                    @endif
                </div>
                <div>
                    @if($question->chapter->questions()->where('id', '>', $question->id)->exists())
                        <a href="{{ route('question-bank.show', $question->chapter->questions()->where('id', '>', $question->id)->orderBy('id', 'asc')->first()->id) }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Next Question<i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const answerForm = document.getElementById('answer-form');

    // Auto-submit when option is selected (optional)
    const radioButtons = answerForm.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Uncomment below to auto-submit on selection
            // answerForm.submit();
        });
    });
});
</script>
@endsection