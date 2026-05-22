@extends('layouts.student')

@section('title', 'Practice Chapter')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('student.practice') }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium);">← Back to Subjects</a>
        <h1 style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-900);">Practice Questions</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Answers are visible - learn as you practice</p>
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
                                <div style="padding: var(--spacing-2); border: 2px solid
                                    @if($option->is_correct) #22c55e @else var(--color-gray-200) @endif;
                                    border-radius: var(--radius-lg); background-color:
                                    @if($option->is_correct) #f0fdf4 @else var(--color-gray-50) @endif;
                                    cursor: pointer; transition: all var(--transition-fast);"
                                    onmouseover="this.style.borderColor='var(--color-primary)'"
                                    onmouseout="this.style.borderColor='
                                    @if($option->is_correct) #22c55e @else var(--color-gray-200) @endif'">

                                    <div style="display: flex; align-items: start;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px;
                                            background-color: @if($option->is_correct) #22c55e @else var(--color-gray-300) @endif;
                                            color: white; border-radius: 50%; font-weight: var(--font-weight-bold); margin-right: var(--spacing-2); display: flex; align-items: center; justify-content: center;">
                                            <svg style="width: 16px; height: 16px;" data-lucide="@if($option->is_correct)check @else minus @endif"></svg>
                                        </span>
                                        <span style="color: var(--color-gray-900); flex: 1;">{{ $option->text }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($question->explanation)
                        <div style="padding: var(--spacing-3); background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: var(--radius-lg); margin-top: var(--spacing-3);">
                            <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-bold); color: #92400e; display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 18px; height: 18px;" data-lucide="lightbulb"></svg>
                                Explanation
                            </p>
                            <p style="margin: 0; color: #92400e; font-size: var(--font-size-sm); line-height: 1.6;">{{ $question->explanation }}</p>
                        </div>
                    @endif
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
</style>
@endsection
