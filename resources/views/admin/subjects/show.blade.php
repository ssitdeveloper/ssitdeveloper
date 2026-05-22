@extends('layouts.admin')

@section('title', 'View Subject')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('admin.subjects.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Subjects</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.subjects.edit', $subject) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block;">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this subject? This will affect all topics and chapters.');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-danger); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm); margin-bottom: var(--spacing-4);">
        <!-- Subject Header -->
        <div style="margin-bottom: var(--spacing-4); padding-bottom: var(--spacing-3); border-bottom: 1px solid var(--color-gray-200);">
            <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">{{ $subject->name }}</h1>
            @if($subject->description)
                <p style="margin: 0; color: var(--color-gray-600); line-height: 1.6;">{{ $subject->description }}</p>
            @endif
        </div>

        <!-- Subject Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-4);">
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Topics</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $subject->topics->count() }}</p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Chapters</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">
                    @php
                        $totalChapters = $subject->topics->sum(function($topic) { return $topic->chapters->count(); });
                    @endphp
                    {{ $totalChapters }}
                </p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Questions</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);">
                    @php
                        $totalQuestions = $subject->topics->sum(function($topic) {
                            return $topic->chapters->sum(function($chapter) {
                                return $chapter->questions->count();
                            });
                        });
                    @endphp
                    {{ $totalQuestions }}
                </p>
            </div>
        </div>
    </div>

    <!-- Topics Section -->
    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h2 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-gray-900);">Topics Under This Subject</h2>

        @if($subject->topics->count() > 0)
            <div style="display: grid; gap: var(--spacing-3);">
                @foreach($subject->topics as $topic)
                    <div style="padding: var(--spacing-3); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); background-color: var(--color-gray-50);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-2);">
                            <div>
                                <h3 style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-900);">{{ $topic->name }}</h3>
                                <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ $topic->chapters->count() }} chapters</p>
                            </div>
                        </div>

                        <!-- Chapters -->
                        @if($topic->chapters->count() > 0)
                            <div style="margin-top: var(--spacing-2); padding-top: var(--spacing-2); border-top: 1px solid var(--color-gray-300);">
                                <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Chapters:</p>
                                <ul style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: var(--spacing-2);">
                                    @foreach($topic->chapters as $chapter)
                                        <li style="padding: var(--spacing-2); background-color: white; border-radius: var(--radius-lg); border: 1px solid var(--color-gray-200);">
                                            <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-medium); color: var(--color-gray-900);">{{ $chapter->name }}</p>
                                            <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ $chapter->questions->count() }} questions</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--color-gray-600); text-align: center; padding: var(--spacing-4);">No topics created for this subject yet.</p>
        @endif
    </div>
</div>
@endsection
