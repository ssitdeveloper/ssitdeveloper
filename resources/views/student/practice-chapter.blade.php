@extends('layouts.student')

@section('title', 'Practice Chapter')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('student.practice') }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium);">← Back to Subjects</a>
        <h1 style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-900);">Practice Questions</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Try to answer questions before revealing the correct answers</p>
    </div>

    @if($questions && count($questions) > 0)
        <div style="display: grid; gap: var(--spacing-4);">
            @foreach($questions as $index => $question)
                <div class="student-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-2);">
                        <h3 style="margin: 0; color: var(--color-gray-900); flex: 1;">Question {{ $index + 1 }}</h3>
                        <span style="padding: var(--spacing-1) var(--spacing-2); background-color: #e0e7ff; color: #4f46e5; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">
                            {{ ucfirst($question->difficulty_level->value ?? 'medium') }}
                        </span>
                    </div>

                    <p style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-900); font-size: var(--font-size-base); line-height: 1.6;">{{ $question->title }}</p>

                    @if($question->options && count($question->options) > 0)
                        <div style="display: grid; gap: var(--spacing-2); margin: var(--spacing-3) 0;">
                            @foreach($question->options as $option)
                                <div class="practice-option"
                                     data-is-correct="{{ $option->is_correct ? 'true' : 'false' }}"
                                     style="padding: var(--spacing-2); border: 2px solid var(--color-gray-200);
                                            border-radius: var(--radius-lg); background-color: var(--color-gray-50);
                                            cursor: pointer; transition: all var(--transition-fast);"
                                    onmouseover="this.style.borderColor='var(--color-primary)'"
                                    onmouseout="this.style.borderColor='var(--color-gray-200)'">

                                    <div style="display: flex; align-items: start;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px;
                                            background-color: var(--color-gray-300);
                                            color: white; border-radius: 50%; font-weight: var(--font-weight-bold); margin-right: var(--spacing-2); display: flex; align-items: center; justify-content: center;">
                                            <svg style="width: 16px; height: 16px;" data-lucide="minus"></svg>
                                        </span>
                                        <span style="color: var(--color-gray-900); flex: 1;">{{ $option->text }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Show Answer Button -->
                    <button class="toggle-answer-btn" onclick="toggleAnswer(this)" style="margin-top: var(--spacing-3); padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; font-size: var(--font-size-sm); transition: all var(--transition-fast);">
                        <svg style="width: 16px; height: 16px; display: inline; margin-right: 6px;" data-lucide="eye"></svg>
                        Show Answer & Explanation
                    </button>

                    <!-- Hidden Answer Section -->
                    <div class="answer-section" style="display: none; padding: var(--spacing-3); background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: var(--radius-lg); margin-top: var(--spacing-3);">
                        <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-bold); color: #92400e; display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 18px; height: 18px;" data-lucide="lightbulb"></svg>
                            Correct Answer & Explanation
                        </p>

                        <!-- Show correct answer -->
                        @if($question->options && count($question->options) > 0)
                            @foreach($question->options as $option)
                                @if($option->is_correct)
                                <div style="padding: var(--spacing-2); background-color: #dbeafe; border-left: 4px solid #0284c7; border-radius: var(--radius-lg); margin-bottom: var(--spacing-2);">
                                    <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-bold); color: #0c4a6e;">✓ Correct Answer:</p>
                                    <p style="margin: 0; color: #0c4a6e;">{{ $option->text }}</p>
                                </div>
                                @endif
                            @endforeach
                        @endif

                        <!-- Explanation -->
                        @if($question->explanation)
                            <p style="margin: var(--spacing-2) 0 0 0; color: #92400e; font-size: var(--font-size-sm); line-height: 1.6;">{{ $question->explanation }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-8);">
            <p style="margin: 0; color: var(--color-gray-500);">No questions available for this chapter.</p>
        </div>
    @endif
</div>

<style>
.student-card {
    background-color: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-4);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.toggle-answer-btn:hover {
    background-color: #0d47a1 !important;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.toggle-answer-btn.active {
    background-color: #4caf50 !important;
}
</style>

<script>
function toggleAnswer(btn) {
    const card = btn.closest('.student-card');
    const answerSection = card.querySelector('.answer-section');
    const options = card.querySelectorAll('.practice-option');

    if (answerSection.style.display === 'none') {
        // Show answer and highlight correct option
        answerSection.style.display = 'block';
        btn.textContent = '✓ Answer Revealed';
        btn.style.backgroundColor = '#4caf50';

        // Highlight correct answer in the options
        options.forEach(option => {
            if (option.getAttribute('data-is-correct') === 'true') {
                option.style.borderColor = '#22c55e';
                option.style.backgroundColor = '#f0fdf4';
                option.querySelector('svg').setAttribute('data-lucide', 'check');
                lucide.createIcons();
            }
        });
    } else {
        // Hide answer
        answerSection.style.display = 'none';
        btn.textContent = '✗ Show Answer & Explanation';
        btn.style.backgroundColor = 'var(--color-primary)';

        // Remove highlighting
        options.forEach(option => {
            option.style.borderColor = 'var(--color-gray-200)';
            option.style.backgroundColor = 'var(--color-gray-50)';
            option.querySelector('svg').setAttribute('data-lucide', 'minus');
            lucide.createIcons();
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection
