@extends('layouts.admin')

@section('title', $chapter->name)

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.chapters.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Chapters</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.chapters.edit', $chapter->id) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Edit</a>
            <form method="POST" action="{{ route('admin.chapters.destroy', $chapter->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: #dc2626; color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Delete</button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); margin-bottom: var(--spacing-4);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">{{ $chapter->name }}</h1>

        <div style="display: grid; gap: var(--spacing-3);">
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Topic</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $chapter->topic->name ?? 'N/A' }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Subject</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ optional($chapter->topic)->subject->name ?? 'N/A' }}</p>
            </div>

            @if($chapter->description)
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Description</p>
                    <p style="margin: 0; color: var(--color-gray-900);">{{ $chapter->description }}</p>
                </div>
            @endif

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Order</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $chapter->order ?? 1 }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Created At</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $chapter->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Last Updated</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $chapter->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
