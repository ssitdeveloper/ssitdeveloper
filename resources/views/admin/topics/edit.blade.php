@extends('layouts.admin')

@section('title', 'Edit Topic')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.topics.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Topics</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); max-width: 600px;">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Edit Topic</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <ul style="margin: 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.topics.update', $topic->id) }}" style="display: grid; gap: var(--spacing-4);">
            @csrf
            @method('PUT')

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Subject <span style="color: #dc2626;">*</span></label>
                <select name="subject_id" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @if($subject->id === $topic->subject_id) selected @endif>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Topic Name <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" value="{{ $topic->name }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Description</label>
                <textarea name="description" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box; min-height: 100px;">{{ $topic->description }}</textarea>
            </div>

            <div style="display: flex; gap: var(--spacing-2);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Update</button>
                <a href="{{ route('admin.topics.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
