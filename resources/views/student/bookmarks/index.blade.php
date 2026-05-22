@extends('layouts.student')

@section('title', 'My Bookmarks')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">My Bookmarks ({{ $bookmarks->total() }})</h1>
    </div>

    @if ($bookmarks->count() > 0)
        <div style="display: grid; gap: var(--spacing-3);">
            @foreach ($bookmarks as $bookmark)
                <div class="student-card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-900);">{{ $bookmark->question->question_text }}</h3>
                            <div style="display: flex; gap: var(--spacing-2); flex-wrap: wrap; margin-bottom: var(--spacing-2);">
                                <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-gray-100); border-radius: var(--radius-lg); font-size: var(--font-size-sm); color: var(--color-gray-700);">
                                    {{ $bookmark->question->chapter->name }}
                                </span>
                                <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-gray-100); border-radius: var(--radius-lg); font-size: var(--font-size-sm); color: var(--color-gray-700);">
                                    Difficulty: {{ ucfirst($bookmark->question->difficulty_level->value) }}
                                </span>
                            </div>
                            <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Bookmarked {{ $bookmark->created_at->diffForHumans() }}</p>
                        </div>
                        <form method="POST" action="{{ route('student.bookmarks.destroy', $bookmark) }}" style="display: inline;" onsubmit="return confirm('Remove bookmark?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: var(--spacing-1) var(--spacing-2); background-color: #dc3545; color: white; border: none; border-radius: var(--radius-lg); cursor: pointer; font-weight: var(--font-weight-semibold);">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if($bookmarks->hasPages())
            <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
                {{ $bookmarks->links() }}
            </div>
        @endif
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-4);">
            <p style="color: var(--color-gray-600); margin: 0;">No bookmarks yet. Start bookmarking questions to save them for later!</p>
        </div>
    @endif
</div>
@endsection
