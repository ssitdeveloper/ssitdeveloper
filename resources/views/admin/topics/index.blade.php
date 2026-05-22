@extends('layouts.admin')

@section('title', 'Manage Topics')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; gap: var(--spacing-3); align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;">Manage Topics</h2>
        <a href="{{ route('admin.topics.create') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block;">
            + Create Topic
        </a>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4); border-left: 4px solid #059669;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                <tr>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Topic Name</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Subject</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Chapters</th>
                    <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topics as $topic)
                    <tr style="border-bottom: 1px solid var(--color-gray-200); hover: background-color var(--color-gray-50);">
                        <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">{{ $topic->name }}</td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $topic->subject->name }}</td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $topic->chapters->count() }}</td>
                        <td style="padding: var(--spacing-2); text-align: center;">
                            <a href="{{ route('admin.topics.show', $topic) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">View</a>
                            <a href="{{ route('admin.topics.edit', $topic) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">Edit</a>
                            <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}" style="display: inline;" onsubmit="return confirm('Delete this topic?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: var(--spacing-4); text-align: center; color: var(--color-gray-500);">No topics found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($topics->hasPages())
        <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
            {{ $topics->links() }}
        </div>
    @endif
</div>
@endsection
