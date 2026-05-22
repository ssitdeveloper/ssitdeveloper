@extends('layouts.app')

@section('title', 'Mock Test - ' . $attempt->test->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Test Header -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $attempt->test->name }}</h4>
                            <small>Question <span id="current-question-number">1</span> of {{ count($attempt->question_ids) }}</small>
                        </div>
                        <div class="text-right">
                            <div class="timer-display" id="timer-display">
                                <i class="fas fa-clock"></i>
                                <span id="timer-text">--:--:--</span>
                            </div>
                            <button class="btn btn-light btn-sm mt-1" onclick="toggleFullscreen()">
                                <i class="fas fa-expand"></i> Fullscreen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Question Panel -->
        <div class="col-lg-8">
            <div class="card" id="question-card">
                <div class="card-body">
                    <div id="question-content">
                        <!-- Question content will be loaded here -->
                        @include('mock-tests.partials.question-display', [
                            'question' => $question,
                            'userAnswer' => $userAnswer,
                            'questionIndex' => 1,
                            'totalQuestions' => count($attempt->question_ids)
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Panel -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Question Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="question-grid" id="question-navigation">
                        @include('mock-tests.partials.question-navigation', ['navigation' => $questionNavigation])
                    </div>

                    <hr>

                    <div class="legend">
                        <div class="d-flex flex-wrap">
                            <span class="badge badge-light mr-2 mb-1">
                                <i class="fas fa-circle text-secondary"></i> Not Visited
                            </span>
                            <span class="badge badge-warning mr-2 mb-1">
                                <i class="fas fa-circle text-warning"></i> Not Answered
                            </span>
                            <span class="badge badge-success mr-2 mb-1">
                                <i class="fas fa-circle text-success"></i> Answered
                            </span>
                            <span class="badge badge-info mr-2 mb-1">
                                <i class="fas fa-circle text-info"></i> Marked for Review
                            </span>
                            <span class="badge badge-danger mr-2 mb-1">
                                <i class="fas fa-circle text-danger"></i> Bookmarked
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="action-buttons">
                        <button class="btn btn-warning btn-block mb-2" onclick="markForReview()">
                            <i class="fas fa-flag"></i> Mark for Review
                        </button>
                        <button class="btn btn-info btn-block mb-2" onclick="bookmarkQuestion()">
                            <i class="fas fa-bookmark"></i> Bookmark
                        </button>
                        <button class="btn btn-success btn-block mb-2" onclick="submitTest()">
                            <i class="fas fa-check"></i> Submit Test
                        </button>
                        <button class="btn btn-secondary btn-block" onclick="pauseTest()">
                            <i class="fas fa-pause"></i> Pause Test
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('mock-tests.partials.modals')

@endsection

@section('scripts')
<script>
let currentAttempt = @json($attempt);
let currentQuestion = @json($question);
let userAnswer = @json($userAnswer);
let timeRemaining = {{ $attempt->getTimeRemainingSeconds() }};
let timerInterval;
let isFullscreen = false;

// Initialize test
$(document).ready(function() {
    startTimer();
    setupKeyboardNavigation();
    setupAutoSave();
});

// Timer functionality
function startTimer() {
    timerInterval = setInterval(function() {
        timeRemaining--;

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            autoSubmitTest();
            return;
        }

        updateTimerDisplay();
        updateTimeOnServer();
    }, 1000);
}

function updateTimerDisplay() {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;

    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    $('#timer-text').text(timeString);

    // Color coding for urgency
    if (timeRemaining < 300) { // Less than 5 minutes
        $('#timer-display').addClass('text-danger').removeClass('text-warning');
    } else if (timeRemaining < 600) { // Less than 10 minutes
        $('#timer-display').addClass('text-warning').removeClass('text-danger');
    }
}

function updateTimeOnServer() {
    // Update time every 30 seconds
    if (timeRemaining % 30 === 0) {
        $.ajax({
            url: '{{ route("mock-tests.update-time", $attempt->id) }}',
            method: 'PATCH',
            data: {
                time_remaining_seconds: timeRemaining,
                _token: '{{ csrf_token() }}'
            }
        });
    }
}

// Question navigation
function loadQuestion(questionId) {
    $.ajax({
        url: '{{ route("mock-tests.question", $attempt->id) }}',
        method: 'GET',
        data: { question_id: questionId },
        success: function(response) {
            currentQuestion = response.question;
            userAnswer = response.user_answer;

            $('#question-content').html(`
                @include('mock-tests.partials.question-display', [
                    'question' => null,
                    'userAnswer' => null,
                    'questionIndex' => 0,
                    'totalQuestions' => 0
                ])
            `.replace('{{ $question->question_text ?? "" }}', response.question.question_text)
             .replace('{{ $questionIndex ?? 0 }}', response.question_index)
             .replace('{{ $totalQuestions ?? 0 }}', response.total_questions));

            $('#current-question-number').text(response.question_index);
            updateNavigationHighlight(questionId);
        }
    });
}

function updateNavigationHighlight(questionId) {
    $('.question-number').removeClass('active');
    $(`.question-number[data-question-id="${questionId}"]`).addClass('active');
}

// Answer saving
function saveAnswer(optionId = null) {
    const markForReview = $('#mark-review-checkbox').is(':checked');

    $.ajax({
        url: '{{ route("mock-tests.save-answer", $attempt->id) }}',
        method: 'POST',
        data: {
            question_id: currentQuestion.id,
            selected_option_id: optionId,
            mark_for_review: markForReview,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            userAnswer = response.answer;
            updateNavigationStatus(currentQuestion.id, optionId !== null, markForReview);
            showToast('Answer saved successfully', 'success');
        }
    });
}

// Navigation status updates
function updateNavigationStatus(questionId, isAnswered, isMarkedForReview) {
    const $navItem = $(`.question-number[data-question-id="${questionId}"]`);

    $navItem.removeClass('not-answered answered marked-review');

    if (isMarkedForReview) {
        $navItem.addClass('marked-review');
    } else if (isAnswered) {
        $navItem.addClass('answered');
    } else {
        $navItem.addClass('not-answered');
    }
}

// Keyboard navigation
function setupKeyboardNavigation() {
    $(document).keydown(function(e) {
        // Number keys for options
        if (e.key >= '1' && e.key <= '4') {
            const optionIndex = parseInt(e.key) - 1;
            const $option = $(`.option-radio:eq(${optionIndex})`);
            if ($option.length) {
                $option.prop('checked', true);
                saveAnswer($option.val());
            }
        }

        // Arrow keys for navigation
        if (e.key === 'ArrowLeft') {
            navigateToPrevious();
        } else if (e.key === 'ArrowRight') {
            navigateToNext();
        }
    });
}

// Navigation functions
function navigateToPrevious() {
    const currentIndex = currentAttempt.question_ids.indexOf(currentQuestion.id);
    if (currentIndex > 0) {
        const prevQuestionId = currentAttempt.question_ids[currentIndex - 1];
        loadQuestion(prevQuestionId);
    }
}

function navigateToNext() {
    const currentIndex = currentAttempt.question_ids.indexOf(currentQuestion.id);
    if (currentIndex < currentAttempt.question_ids.length - 1) {
        const nextQuestionId = currentAttempt.question_ids[currentIndex + 1];
        loadQuestion(nextQuestionId);
    }
}

// Mark for review
function markForReview() {
    const isChecked = !$('#mark-review-checkbox').is(':checked');
    $('#mark-review-checkbox').prop('checked', isChecked);
    saveAnswer(userAnswer ? userAnswer.selected_option_id : null);
}

// Bookmark question
function bookmarkQuestion() {
    const notes = prompt('Add notes for this question (optional):');

    $.ajax({
        url: '{{ route("mock-tests.bookmark", $attempt->id) }}',
        method: 'POST',
        data: {
            question_id: currentQuestion.id,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            showToast('Question bookmarked', 'success');
            updateBookmarkStatus(currentQuestion.id, true);
        }
    });
}

function updateBookmarkStatus(questionId, isBookmarked) {
    const $navItem = $(`.question-number[data-question-id="${questionId}"]`);
    if (isBookmarked) {
        $navItem.addClass('bookmarked');
    } else {
        $navItem.removeClass('bookmarked');
    }
}

// Auto-save functionality
function setupAutoSave() {
    setInterval(function() {
        // Auto-save current answer every 30 seconds
        if (userAnswer && userAnswer.selected_option_id) {
            saveAnswer(userAnswer.selected_option_id);
        }
    }, 30000);
}

// Fullscreen functionality
function toggleFullscreen() {
    if (!isFullscreen) {
        document.documentElement.requestFullscreen();
        isFullscreen = true;
    } else {
        document.exitFullscreen();
        isFullscreen = false;
    }
}

// Submit test
function submitTest() {
    if (confirm('Are you sure you want to submit the test? This action cannot be undone.')) {
        $('#submit-test-form').submit();
    }
}

function autoSubmitTest() {
    alert('Time is up! Your test will be submitted automatically.');
    $('#submit-test-form').submit();
}

function pauseTest() {
    if (confirm('Are you sure you want to pause the test? You can resume it later.')) {
        window.location.href = '{{ route("student.dashboard") }}';
    }
}

// Toast notifications
function showToast(message, type = 'info') {
    // Simple toast implementation - you can replace with a proper toast library
    const toast = $(`<div class="alert alert-${type} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>`);

    $('body').append(toast);
    setTimeout(() => toast.alert('close'), 3000);
}

// Form for submitting test
document.write(`
    <form id="submit-test-form" action="{{ route('mock-tests.submit', $attempt->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
`);
</script>
@endsection