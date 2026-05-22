@extends('layouts.student')

@section('title', 'My Bookmarks')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Bookmarked Questions</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Your saved questions for quick reference.</p>
    </div>

    @if ($bookmarks->isEmpty())
        <div class="student-card" style="text-align: center; padding: var(--spacing-8);">
            <p style="margin: 0; color: var(--color-gray-500);">No bookmarked questions yet. Start bookmarking questions during practice!</p>
        </div>
    @else
        <div style="display: grid; gap: var(--spacing-4);">
            @foreach ($bookmarks as $question)
                <div class="student-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--spacing-3);">
                        <h3 style="margin: 0; font-size: var(--font-size-base); color: var(--color-gray-900); font-weight: var(--font-weight-medium); flex: 1;">{{ $question->question_text }}</h3>
                        <span style="background-color: {{ optional($question->chapter->topic->subject)->color ?? 'var(--color-primary)' }}20; color: {{ optional($question->chapter->topic->subject)->color ?? 'var(--color-primary)' }}; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--radius-lg); font-size: var(--font-size-xs); font-weight: var(--font-weight-medium); white-space: nowrap; margin-left: var(--spacing-2);">
                            {{ $question->difficulty_level }}
                        </span>
                    </div>

                    <p style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">
                        {{ $question->chapter->topic->subject->name }} > {{ $question->chapter->topic->name }} > {{ $question->chapter->name }}
                    </p>

                    <div style="display: grid; gap: var(--spacing-2); margin-bottom: var(--spacing-4);">
                        @foreach ($question->options as $option)
                            <label style="display: flex; align-items: center; padding: var(--spacing-2); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); cursor: pointer; transition: all var(--transition-fast);" onmouseover="this.style.backgroundColor='var(--color-gray-50)'" onmouseout="this.style.backgroundColor='transparent'">
                                <input type="radio" name="option_{{ $question->id }}" value="{{ $option->id }}" style="margin-right: var(--spacing-2); cursor: pointer;">
                                <span style="color: var(--color-gray-900);">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if($question->explanation)
                        <div style="background-color: var(--color-gray-50); border-left: 3px solid var(--color-primary); padding: var(--spacing-3); margin-bottom: var(--spacing-3); border-radius: var(--radius-lg);">
                            <p style="margin: 0; color: var(--color-gray-700); font-size: var(--font-size-sm);">
                                <strong>Explanation:</strong> {{ $question->explanation }}
                            </p>
                        </div>
                    @endif

                    <div style="display: flex; gap: var(--spacing-2); justify-content: flex-end;">
                        <form method="POST" action="{{ route('student.bookmarks.destroy', $question->id) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-danger); color: var(--color-white); border: none; border-radius: var(--radius-lg); cursor: pointer; font-weight: var(--font-weight-medium); font-size: var(--font-size-sm); transition: all var(--transition-fast);" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='var(--color-danger)'">
                                🗑️ Remove
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
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
