@extends('layouts.student')

@section('title', $test->title)

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('student.tests') }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium);">← Back to Tests</a>
        <h1 style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-900);">{{ $test->title }}</h1>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-4);">
        <!-- Test Info -->
        <div class="student-card">
            <h3 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-900);">Test Details</h3>

            <div style="display: grid; gap: var(--spacing-2);">
                <div>
                    <p style="margin: 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Duration</p>
                    <p style="margin: var(--spacing-1) 0 0 0; font-weight: var(--font-weight-bold); font-size: var(--font-size-lg); color: var(--color-primary);">{{ $test->duration_minutes }} minutes</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Total Questions</p>
                    <p style="margin: var(--spacing-1) 0 0 0; font-weight: var(--font-weight-bold); font-size: var(--font-size-lg); color: var(--color-primary);">{{ $test->total_questions ?? 0 }}</p>
                </div>
                @if($test->marks_per_question)
                <div>
                    <p style="margin: 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Marks per Question</p>
                    <p style="margin: var(--spacing-1) 0 0 0; font-weight: var(--font-weight-bold); font-size: var(--font-size-lg); color: var(--color-primary);">{{ $test->marks_per_question }}</p>
                </div>
                @endif
                @if($test->negative_marking)
                <div>
                    <p style="margin: 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Negative Marking</p>
                    <p style="margin: var(--spacing-1) 0 0 0; font-weight: var(--font-weight-bold); font-size: var(--font-size-lg); color: #f59e0b;">{{ $test->negative_marking }}</p>
                </div>
                @endif
            </div>

            <div style="margin-top: var(--spacing-3); padding-top: var(--spacing-3); border-top: 1px solid var(--color-gray-200);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Your Attempts</p>
                <p style="margin: 0; font-weight: var(--font-weight-bold); font-size: var(--font-size-lg);">{{ $userAttempts }}</p>
            </div>
        </div>

        <!-- Instructions -->
        <div class="student-card">
            <h3 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-900);">Instructions</h3>

            @if($test->instructions && is_array($test->instructions))
                <ul style="margin: 0; padding-left: var(--spacing-4); color: var(--color-gray-700); font-size: var(--font-size-sm); line-height: 1.6;">
                    @foreach($test->instructions as $instruction)
                        <li style="margin-bottom: var(--spacing-2);">{{ $instruction }}</li>
                    @endforeach
                </ul>
            @else
                <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg); color: var(--color-gray-600);">
                    <ul style="margin: 0; padding-left: var(--spacing-4); font-size: var(--font-size-sm); line-height: 1.6;">
                        <li style="margin-bottom: var(--spacing-2);">Read all questions carefully before answering</li>
                        <li style="margin-bottom: var(--spacing-2);">You have {{ $test->duration_minutes }} minutes to complete this test</li>
                        <li style="margin-bottom: var(--spacing-2);">Once you submit, you cannot change your answers</li>
                        <li style="margin-bottom: var(--spacing-2);">Make sure you have a stable internet connection</li>
                        <li>Click "Submit Test" to finish</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <!-- Description -->
    @if($test->description)
        <div class="student-card" style="margin-top: var(--spacing-4);">
            <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">About This Test</h3>
            <p style="margin: 0; color: var(--color-gray-700); line-height: 1.6;">{{ $test->description }}</p>
        </div>
    @endif

    <!-- Start Test Button -->
    <div style="margin-top: var(--spacing-4); display: flex; gap: var(--spacing-3); justify-content: center;">
        <form method="POST" action="{{ route('student.tests.start', $test->slug) }}" style="display: inline;">
            @csrf
            <input type="hidden" name="test_id" value="{{ $test->id }}">
            <button type="submit" style="padding: var(--spacing-3) var(--spacing-6); background-color: #22c55e; color: white; border: none; border-radius: var(--radius-lg); font-size: var(--font-size-base); font-weight: var(--font-weight-bold); cursor: pointer; transition: all var(--transition-fast);" onmouseover="this.style.backgroundColor='#16a34a'" onmouseout="this.style.backgroundColor='#22c55e'">
                🚀 Start Test Now
            </button>
        </form>
        <a href="{{ route('student.tests') }}" style="padding: var(--spacing-3) var(--spacing-6); background-color: var(--color-gray-300); color: var(--color-gray-900); border: none; border-radius: var(--radius-lg); font-size: var(--font-size-base); font-weight: var(--font-weight-bold); cursor: pointer; text-decoration: none; display: inline-block; transition: all var(--transition-fast);" onmouseover="this.style.backgroundColor='var(--color-gray-400)'" onmouseout="this.style.backgroundColor='var(--color-gray-300)'">
            ← Back
        </a>
    </div>
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
