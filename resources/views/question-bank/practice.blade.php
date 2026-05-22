@extends('layouts.app')

@section('title', 'Practice Mode')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Practice Mode</h1>
                    <p class="mt-2 text-gray-600">Random questions to test your knowledge</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('question-bank.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Question Bank
                    </a>
                    <button onclick="location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-random mr-2"></i>New Set
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($questions->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-question-circle text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No questions available</h3>
                <p class="text-gray-600 mb-6">Try adjusting your filters or check back later.</p>
                <a href="{{ route('question-bank.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse All Questions
                </a>
            </div>
        @else
            <!-- Progress -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Practice Session</h2>
                    <span class="text-sm text-gray-600">{{ $questions->count() }} questions</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%" id="progress-bar"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mt-2">
                    <span id="current-question">Question 1 of {{ $questions->count() }}</span>
                    <span id="score-display">Score: 0/{{ $questions->count() }}</span>
                </div>
            </div>

            <!-- Questions Container -->
            <div id="questions-container">
                @foreach($questions as $index => $question)
                    <div class="question-card bg-white rounded-lg shadow-sm p-8 mb-6 {{ $index > 0 ? 'hidden' : '' }}" data-question-id="{{ $question->id }}">
                        <div class="mb-6">
                            <div class="flex items-center space-x-2 mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($question->difficulty_level === 'easy') bg-green-100 text-green-800
                                    @elseif($question->difficulty_level === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($question->difficulty_level->value) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $question->chapter->topic->subject->name }} >
                                    {{ $question->chapter->topic->name }} >
                                    {{ $question->chapter->name }}
                                </span>
                            </div>

                            <div class="prose prose-lg max-w-none mb-6">
                                {!! $question->question_text !!}
                            </div>

                            <form class="answer-form" data-question-id="{{ $question->id }}">
                                @csrf
                                <div class="space-y-3">
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
                            </form>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <span><i class="fas fa-eye mr-1"></i>{{ $question->views_count }} views</span>
                                <span class="ml-4"><i class="fas fa-check-circle mr-1"></i>{{ $question->attempts_count }} attempts</span>
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" class="answer-btn bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium" data-question-id="{{ $question->id }}">
                                    Submit Answer
                                </button>
                                @if(!$loop->last)
                                    <button type="button" class="next-btn bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium hidden">
                                        Next Question
                                    </button>
                                @else
                                    <button type="button" class="finish-btn bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium hidden">
                                        Finish Practice
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Results Modal (hidden by default) -->
            <div id="results-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Practice Complete!</h3>
                        <div class="mb-6">
                            <div class="text-4xl font-bold text-blue-600 mb-2" id="final-score">0/{{ $questions->count() }}</div>
                            <div class="text-gray-600" id="accuracy">0% accuracy</div>
                        </div>
                        <div class="flex space-x-3 justify-center">
                            <button onclick="location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Practice Again
                            </button>
                            <a href="{{ route('question-bank.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                Back to Question Bank
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if(!$questions->isEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionCards = document.querySelectorAll('.question-card');
    const progressBar = document.getElementById('progress-bar');
    const currentQuestionDisplay = document.getElementById('current-question');
    const scoreDisplay = document.getElementById('score-display');
    const resultsModal = document.getElementById('results-modal');
    const finalScore = document.getElementById('final-score');
    const accuracy = document.getElementById('accuracy');

    let currentQuestionIndex = 0;
    let score = 0;
    let answers = [];

    function updateProgress() {
        const progress = ((currentQuestionIndex + 1) / questionCards.length) * 100;
        progressBar.style.width = progress + '%';
        currentQuestionDisplay.textContent = `Question ${currentQuestionIndex + 1} of ${questionCards.length}`;
        scoreDisplay.textContent = `Score: ${score}/${questionCards.length}`;
    }

    function showQuestion(index) {
        questionCards.forEach((card, i) => {
            card.classList.toggle('hidden', i !== index);
        });
        updateProgress();
    }

    function submitAnswer(questionId, selectedOptionId) {
        fetch(`/api/questions/${questionId}/validate-answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.getAttribute('content') || ''}`
            },
            body: JSON.stringify({
                selected_option_id: selectedOptionId
            })
        })
        .then(response => response.json())
        .then(data => {
            answers.push(data.data);

            if (data.data.is_correct) {
                score++;
            }

            // Show result in the current card
            const currentCard = questionCards[currentQuestionIndex];
            const resultDiv = document.createElement('div');
            resultDiv.className = `mt-6 p-4 rounded-lg ${data.data.is_correct ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'}`;

            resultDiv.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        ${data.data.is_correct ?
                            '<i class="fas fa-check text-green-600 text-xl"></i>' :
                            '<i class="fas fa-times text-red-600 text-xl"></i>'
                        }
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium ${data.data.is_correct ? 'text-green-900' : 'text-red-900'} mb-2">
                            ${data.data.is_correct ? 'Correct!' : 'Incorrect'}
                        </h4>
                        ${data.data.explanation ? `
                            <div class="text-sm ${data.data.is_correct ? 'text-green-800' : 'text-red-800'} mb-2">
                                <strong>Explanation:</strong> ${data.data.explanation}
                            </div>
                        ` : ''}
                        ${!data.data.is_correct && data.data.correct_option ? `
                            <div class="text-sm text-green-800">
                                <strong>Correct answer:</strong> ${data.data.correct_option.option_text}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

            currentCard.appendChild(resultDiv);

            // Hide submit button and show next/finish button
            currentCard.querySelector('.answer-btn').classList.add('hidden');
            const nextBtn = currentCard.querySelector('.next-btn, .finish-btn');
            if (nextBtn) {
                nextBtn.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting answer. Please try again.');
        });
    }

    // Event listeners for answer buttons
    document.querySelectorAll('.answer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionId = this.dataset.questionId;
            const form = this.closest('.question-card').querySelector('.answer-form');
            const selectedOption = form.querySelector('input[type="radio"]:checked');

            if (!selectedOption) {
                alert('Please select an answer first.');
                return;
            }

            submitAnswer(questionId, selectedOption.value);
        });
    });

    // Event listeners for next/finish buttons
    document.querySelectorAll('.next-btn, .finish-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.classList.contains('finish-btn')) {
                // Show results
                const totalQuestions = questionCards.length;
                const accuracyPercent = Math.round((score / totalQuestions) * 100);

                finalScore.textContent = `${score}/${totalQuestions}`;
                accuracy.textContent = `${accuracyPercent}% accuracy`;

                resultsModal.classList.remove('hidden');
            } else {
                // Show next question
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
            }
        });
    });

    // Initialize
    updateProgress();
});
</script>
@endif
@endsection