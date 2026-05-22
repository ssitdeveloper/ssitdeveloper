@extends('layouts.admin')

@section('title', 'Create Test')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.tests.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Tests</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Create New Test</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <strong>Error:</strong>
                <ul style="margin: var(--spacing-2) 0 0 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.tests.store') }}" style="display: grid; gap: var(--spacing-4);">
            @csrf

            <!-- Test Name -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Test Name <span style="color: var(--color-danger);">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;" placeholder="e.g., Full Length Mock Test 1">
            </div>

            <!-- Description -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Description</label>
                <textarea name="description" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; min-height: 100px;">{{ old('description') }}</textarea>
            </div>

            <!-- Total Questions -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Total Questions <span style="color: var(--color-danger);">*</span></label>
                <input type="number" name="total_questions" value="{{ old('total_questions') }}" required min="1" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;">
            </div>

            <!-- Duration (in minutes) -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Duration (minutes) <span style="color: var(--color-danger);">*</span></label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" required min="1" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;">
            </div>

            <!-- Passing Score -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Passing Score (%) <span style="color: var(--color-danger);">*</span></label>
                <input type="number" name="passing_score" value="{{ old('passing_score', 40) }}" required min="0" max="100" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;">
            </div>

            <!-- Test Status -->
            <div style="display: flex; align-items: center; gap: var(--spacing-2); padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg); border: 1px solid var(--color-gray-300);">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true)) style="width: 20px; height: 20px; cursor: pointer;">
                <label for="is_active" style="margin: 0; font-weight: var(--font-weight-medium); color: var(--color-gray-900); cursor: pointer; flex: 1;">
                    Activate this test immediately
                </label>
                <span style="color: var(--color-gray-600); font-size: var(--font-size-sm);">Active tests are visible to students</span>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: var(--spacing-2); margin-top: var(--spacing-4);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; transition: all var(--transition-fast);">
                    Create Test
                </button>
                <a href="{{ route('admin.tests.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; text-decoration: none; display: inline-block; transition: all var(--transition-fast);">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
