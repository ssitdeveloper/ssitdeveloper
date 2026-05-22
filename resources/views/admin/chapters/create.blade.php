@extends('layouts.admin')

@section('title', 'Create Chapter')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.chapters.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Chapters</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); max-width: 600px;">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Create New Chapter</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <ul style="margin: 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.chapters.store') }}" style="display: grid; gap: var(--spacing-4);">
            @csrf

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Topic <span style="color: #dc2626;">*</span></label>
                <select name="topic_id" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <option value="">Select Topic</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}">{{ $topic->subject->name }} - {{ $topic->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Chapter Name <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Description</label>
                <textarea name="description" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box; min-height: 100px;"></textarea>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Order</label>
                <input type="number" name="order" min="1" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: var(--spacing-2);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Create</button>
                <a href="{{ route('admin.chapters.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
