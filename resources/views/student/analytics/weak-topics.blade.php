@extends('layouts.student')

@section('title', 'Weak Topics Analysis')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Weak Topics Analysis</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Topics where you need improvement</p>
    </div>

    @if ($weakTopics->count() > 0)
        <div style="display: grid; gap: var(--spacing-3);">
            @foreach ($weakTopics as $weak)
                <div class="student-card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-900);">{{ $weak->topic->name }}</h3>
                            <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ $weak->topic->subject->name }}</p>

                            <!-- Progress Bar -->
                            <div style="background-color: var(--color-gray-200); height: 8px; border-radius: var(--radius-lg); overflow: hidden; margin: var(--spacing-2) 0;">
                                <div style="background-color: #f59e0b; height: 100%; width: {{ $weak->weak_score }}%;"></div>
                            </div>

                            <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">
                                {{ $weak->weak_score }}% correct | {{ $weak->topic->chapters->count() }} chapters
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('student.practice.chapter', $weak->topic->chapters->first()) }}" style="padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); display: inline-block;">
                                Practice
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($weakTopics->hasPages())
            <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
                {{ $weakTopics->links() }}
            </div>
        @endif
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-4);">
            <p style="color: var(--color-gray-600); margin: 0;">Great job! No weak topics identified. Keep practicing to maintain your score!</p>
        </div>
    @endif
</div>
@endsection
