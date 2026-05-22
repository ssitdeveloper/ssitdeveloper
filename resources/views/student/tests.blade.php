@extends('layouts.student')

@section('title', 'Take Tests')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Available Tests</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">Practice with full-length mocks and subject-wise tests</p>
    </div>

    @if($tests && count($tests) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-4);">
            @foreach($tests as $test)
                <div class="student-card" style="display: flex; flex-direction: column;">
                    <h3 style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-900); font-size: var(--font-size-base);">{{ $test->title }}</h3>

                    <p style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ Str::limit($test->description, 100) }}</p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-2); margin: var(--spacing-2) 0; padding: var(--spacing-2); background-color: var(--color-gray-50); border-radius: var(--radius-lg); flex-grow: 1;">
                        <div>
                            <p style="margin: 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Duration</p>
                            <p style="margin: var(--spacing-1) 0 0 0; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $test->duration_minutes }} min</p>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: var(--font-size-sm); color: var(--color-gray-600);">Questions</p>
                            <p style="margin: var(--spacing-1) 0 0 0; font-weight: var(--font-weight-bold); color: var(--color-primary);">{{ $test->total_questions ?? 0 }}</p>
                        </div>
                    </div>

                    <a href="{{ route('student.tests.show', $test) }}" style="margin-top: auto; padding: var(--spacing-2) var(--spacing-3); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); text-align: center; display: inline-block; width: 100%; cursor: pointer; transition: all var(--transition-fast);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Start Test →
                    </a>
                </div>
            @endforeach
        </div>

        @if($tests->hasPages())
            <div style="display: flex; justify-content: center; margin-top: var(--spacing-4);">
                {{ $tests->links() }}
            </div>
        @endif
    @else
        <div class="student-card" style="text-align: center; padding: var(--spacing-8);">
            <p style="margin: 0; color: var(--color-gray-500); font-size: var(--font-size-base);">No tests available at the moment. Check back soon!</p>
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
    transition: all var(--transition-fast);
}

.student-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--color-primary);
}
</style>
@endsection
