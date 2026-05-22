@extends('layouts.student')

@section('title', 'Practice Mode')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Practice Mode</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Learn by practicing! Select a subject and topic to start.</p>
    </div>

    @if($subjects && count($subjects) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--spacing-3);">
            @foreach($subjects as $subject)
                <div class="student-card">
                    <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">{{ $subject->name }}</h3>
                    <p style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ count($subject->topics ?? []) }} topics available</p>

                    @if($subject->topics && count($subject->topics) > 0)
                        <div style="display: grid; gap: var(--spacing-2);">
                            @foreach($subject->topics as $topic)
                                <a href="{{ route('student.practice.chapter', $topic->chapters->first()->id ?? '#') }}"
                                   style="padding: var(--spacing-2); background-color: var(--color-gray-50); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium); font-size: var(--font-size-sm); transition: all var(--transition-fast); display: flex; align-items: center; justify-content: center; gap: 8px;"
                                   class="practice-topic-link">
                                    <svg style="width: 18px; height: 18px;" data-lucide="book"></svg>
                                    {{ $topic->name }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p style="margin: 0; color: var(--color-gray-500); font-size: var(--font-size-sm); text-align: center;">No topics available</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-8);">
            <p style="margin: 0; color: var(--color-gray-500);">No subjects available for practice. Check back soon!</p>
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

.practice-topic-link:hover {
    background-color: var(--color-gray-100) !important;
    border-color: var(--color-primary) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
