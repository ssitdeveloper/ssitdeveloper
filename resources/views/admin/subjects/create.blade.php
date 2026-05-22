@extends('layouts.admin')

@section('title', 'Create Subject')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.subjects.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Subjects</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Create New Subject</h1>

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

        <form method="POST" action="{{ route('admin.subjects.store') }}" style="display: grid; gap: var(--spacing-4);">
            @csrf

            <!-- Subject Name -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Subject Name <span style="color: var(--color-danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;" placeholder="e.g., Physics, Chemistry, Biology">
            </div>

            <!-- Description -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Description</label>
                <textarea name="description" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; min-height: 100px;" placeholder="Describe this subject...">{{ old('description') }}</textarea>
            </div>

            <!-- Color -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Color <span style="color: var(--color-danger);">*</span></label>
                <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); cursor: pointer; height: 40px;">
            </div>

            <!-- Order -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Order <span style="color: var(--color-danger);">*</span></label>
                <input type="number" name="order_by" value="{{ old('order_by', 0) }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box;" placeholder="Display order">
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: var(--spacing-2); margin-top: var(--spacing-4);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; transition: all var(--transition-fast);">
                    Create Subject
                </button>
                <a href="{{ route('admin.subjects.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; text-decoration: none; display: inline-block; transition: all var(--transition-fast);">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
