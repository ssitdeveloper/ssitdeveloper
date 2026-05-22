@extends('layouts.admin')

@section('title', 'View Question')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('admin.questions.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Questions</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.questions.edit', $question) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block;">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-danger); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm); margin-bottom: var(--spacing-4);">
        <!-- Question Header -->
        <div style="margin-bottom: var(--spacing-4); padding-bottom: var(--spacing-3); border-bottom: 1px solid var(--color-gray-200);">
            <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">{{ $question->question_text }}</h1>
            <div style="display: flex; gap: var(--spacing-3); flex-wrap: wrap;">
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Chapter</p>
                    <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $question->chapter->name }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Topic</p>
                    <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $question->chapter->topic->name }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Subject</p>
                    <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $question->chapter->topic->subject->name }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Difficulty Level</p>
                    <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background: var(--color-gray-200); border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">
                        {{ ucfirst($question->difficulty_level->value) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Question Text -->
        <div style="margin-bottom: var(--spacing-4);">
            <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Question</h3>
            <div style="background-color: var(--color-gray-50); padding: var(--spacing-3); border-radius: var(--radius-lg); border-left: 4px solid var(--color-primary);">
                <p style="margin: 0; color: var(--color-gray-900); line-height: 1.6;">{{ $question->question_text }}</p>
            </div>
        </div>

        <!-- Options -->
        <div style="margin-bottom: var(--spacing-4);">
            <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Options</h3>
            <div style="display: grid; gap: var(--spacing-2);">
                @forelse($question->options as $index => $option)
                    <div style="padding: var(--spacing-3); background-color: {{ $option->is_correct ? 'rgba(16, 185, 129, 0.1)' : 'var(--color-gray-50)' }}; border-radius: var(--radius-lg); border-left: 4px solid {{ $option->is_correct ? '#10b981' : 'var(--color-gray-300)' }};">
                        <div style="display: flex; gap: var(--spacing-2); align-items: flex-start;">
                            <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-gray-200); border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); min-width: 40px; text-align: center;">
                                {{ chr(65 + $index) }}
                            </span>
                            <div style="flex: 1;">
                                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-900);">{{ $option->option_text }}</p>
                                @if($option->is_correct)
                                    <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #10b981; color: white; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold);">✓ Correct Answer</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--color-gray-600);">No options added yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Explanation -->
        @if($question->explanation)
            <div>
                <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Explanation</h3>
                <div style="background-color: rgba(59, 130, 246, 0.1); padding: var(--spacing-3); border-radius: var(--radius-lg); border-left: 4px solid #3b82f6;">
                    <p style="margin: 0; color: var(--color-gray-900); line-height: 1.6;">{{ $question->explanation }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-4);">
        <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); text-align: center;">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Attempts</p>
            <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $question->answers->count() }}</p>
        </div>
        <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); text-align: center;">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Correct Answers</p>
            <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: #10b981;">{{ $question->answers()->where('is_correct', true)->count() }}</p>
        </div>
        <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); text-align: center;">
            <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Success Rate</p>
            <p style="margin: 0; font-size: 2rem; font-weight: var(--font-weight-bold); color: #f59e0b;">
                @if($question->answers->count() > 0)
                    {{ round(($question->answers()->where('is_correct', true)->count() / $question->answers->count()) * 100) }}%
                @else
                    0%
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
