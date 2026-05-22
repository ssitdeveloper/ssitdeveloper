@extends('layouts.admin')

@section('title', 'Chapters')

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Chapters</h1>
        <a href="{{ route('admin.chapters.create') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">+ Create Chapter</a>
    </div>

    @if ($chapters->isEmpty())
        <div class="card" style="text-align: center; padding: var(--spacing-8); background-color: var(--color-gray-50);">
            <p style="margin: 0; color: var(--color-gray-500);">No chapters found. Create one to get started.</p>
        </div>
    @else
        <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Chapter Name</th>
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Topic</th>
                        <th style="text-align: left; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Subject</th>
                        <th style="text-align: center; padding: var(--spacing-3); font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chapters as $chapter)
                        <tr style="border-bottom: 1px solid var(--color-gray-100);">
                            <td style="padding: var(--spacing-3); color: var(--color-gray-900);">{{ $chapter->name }}</td>
                            <td style="padding: var(--spacing-3); color: var(--color-gray-700);">{{ $chapter->topic->name ?? 'N/A' }}</td>
                            <td style="padding: var(--spacing-3); color: var(--color-gray-700);">{{ optional($chapter->topic)->subject->name ?? 'N/A' }}</td>
                            <td style="padding: var(--spacing-3); text-align: center;">
                                <div style="display: flex; justify-content: center; gap: var(--spacing-2);">
                                    <a href="{{ route('admin.chapters.edit', $chapter->id) }}" style="padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Edit</a>
                                    <form method="POST" action="{{ route('admin.chapters.destroy', $chapter->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: var(--spacing-1) var(--spacing-2); background-color: #dc2626; color: white; border: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm); cursor: pointer;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
