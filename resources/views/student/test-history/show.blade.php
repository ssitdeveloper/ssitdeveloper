@extends('layouts.student')

@section('title', 'Test Details - ' . ($attempt->test->title ?? 'Test'))

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('student.test-history') }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium);">← Back to Test History</a>
        <h1 style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-900);">{{ $attempt->test->title ?? 'Test' }}</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Completed on {{ $attempt->created_at->format('M d, Y H:i') }}</p>
    </div>

    <!-- Test Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-4);">
        <div class="student-card">
            <div style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-bottom: var(--spacing-1);">Your Score</div>
            <div style="font-size: 2rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">
                {{ round(($attempt->marks_obtained / ($attempt->test->total_marks ?? 1)) * 100, 1) }}%
            </div>
            <div style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-top: var(--spacing-1);">{{ $attempt->marks_obtained }} / {{ $attempt->test->total_marks ?? 0 }} marks</div>
        </div>
        <div class="student-card">
            <div style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-bottom: var(--spacing-1);">Status</div>
            <div style="font-size: 1.5rem; font-weight: var(--font-weight-bold);
                @if($attempt->status === 'passed') color: #22c55e; @elseif($attempt->status === 'failed') color: #dc3545; @else color: var(--color-gray-600); @endif">
                {{ ucfirst($attempt->status) }}
            </div>
        </div>
        <div class="student-card">
            <div style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-bottom: var(--spacing-1);">Time Spent</div>
            <div style="font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">
                {{ $attempt->time_spent_minutes ?? 0 }} min
            </div>
        </div>
        <div class="student-card">
            <div style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-bottom: var(--spacing-1);">Questions</div>
            <div style="font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">
                {{ $attempt->test->questions_count ?? count($attempt->answers) }} / {{ $attempt->test->total_questions ?? 0 }}
            </div>
        </div>
    </div>

    <!-- Question Review -->
    <div class="student-card">
        <h3 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-900);">Question Review</h3>

        @if($attempt->answers && count($attempt->answers) > 0)
            <div style="display: grid; gap: var(--spacing-3);">
                @foreach($attempt->answers as $index => $answer)
                    <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-left: 4px solid
                        @if($answer->is_correct) #22c55e @else #dc3545 @endif;
                        border-radius: var(--radius-lg);">

                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-2);">
                            <h4 style="margin: 0; color: var(--color-gray-900); font-size: var(--font-size-base);">
                                Question {{ $index + 1 }}
                            </h4>
                            <span style="padding: var(--spacing-1) var(--spacing-2); background-color:
                                @if($answer->is_correct) #d4edda @else #f8d7da @endif;
                                color: @if($answer->is_correct) #155724 @else #721c24 @endif;
                                border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); display: flex; align-items: center; gap: 6px; width: fit-content;">
                                <svg style="width: 14px; height: 14px;" data-lucide="@if($answer->is_correct)check @else x @endif"></svg>
                                @if($answer->is_correct) Correct @else Incorrect @endif
                            </span>
                        </div>

                        <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900); font-weight: var(--font-weight-medium);">
                            {{ $answer->question->title ?? 'Question' }}
                        </p>

                        <div style="background-color: white; padding: var(--spacing-2); border-radius: var(--radius-lg); margin: var(--spacing-2) 0;">
                            <p style="margin: 0 0 var(--spacing-1) 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Your Answer:</p>
                            <p style="margin: 0; color: var(--color-gray-900); font-weight: var(--font-weight-medium);">
                                {{ $answer->option->text ?? 'No answer selected' }}
                            </p>
                        </div>

                        @if(!$answer->is_correct && $answer->question)
                            <div style="background-color: #d4edda; padding: var(--spacing-2); border-radius: var(--radius-lg); margin: var(--spacing-2) 0;">
                                <p style="margin: 0 0 var(--spacing-1) 0; font-size: var(--font-size-sm); color: #155724; font-weight: var(--font-weight-medium);">Correct Answer:</p>
                                <p style="margin: 0; color: #155724;">
                                    {{ $answer->question->correctOption->text ?? 'N/A' }}
                                </p>
                            </div>
                        @endif

                        @if($answer->question && $answer->question->explanation)
                            <div style="background-color: #fff3cd; padding: var(--spacing-2); border-radius: var(--radius-lg); margin: var(--spacing-2) 0;">
                                <p style="margin: 0 0 var(--spacing-1) 0; font-size: var(--font-size-sm); color: #856404; font-weight: var(--font-weight-medium);">Explanation:</p>
                                <p style="margin: 0; color: #856404; font-size: var(--font-size-sm);">
                                    {{ $answer->question->explanation }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--color-gray-600); text-align: center; padding: var(--spacing-4);">No questions were answered in this test.</p>
        @endif
    </div>
</div>
@endsection
