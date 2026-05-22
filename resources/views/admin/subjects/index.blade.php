@extends('layouts.admin')

@section('title', 'Manage Subjects')

@section('content')
    <div style="margin-bottom: var(--spacing-4); display: flex; gap: var(--spacing-3); align-items: center;">
        <h2 style="margin: 0; flex: 1;">Manage Subjects</h2>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">+ Add New Subject</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4);">
            {{ session('success') }}
        </div>
    @endif

    <!-- Subjects Table -->
    <div class="admin-table" style="background: white; border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--color-gray-100); border-bottom: 1px solid var(--color-gray-300);">
                <tr>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Subject Name</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Color</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Order</th>
                    <th style="padding: var(--spacing-3); text-align: center; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects->sortBy('order_by') as $subject)
                    <tr style="border-bottom: 1px solid var(--color-gray-200); transition: background 0.2s;">
                        <td style="padding: var(--spacing-3);">#{{ $subject->id }}</td>
                        <td style="padding: var(--spacing-3);">
                            {{ $subject->name }}
                        </td>
                        <td style="padding: var(--spacing-3);">
                            <div style="display: flex; align-items: center; gap: var(--spacing-2);">
                                <div style="width: 24px; height: 24px; background: {{ $subject->color }}; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1);"></div>
                                <span>{{ $subject->color }}</span>
                            </div>
                        </td>
                        <td style="padding: var(--spacing-3);">
                            {{ $subject->order_by }}
                        </td>
                        <td style="padding: var(--spacing-3); text-align: center;">
                            <a href="{{ route('admin.subjects.edit', $subject) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">Edit</a>
                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" style="display: inline;" onsubmit="return confirm('Delete this subject?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: var(--spacing-4); text-align: center; color: var(--color-gray-500);">
                            No subjects found. <a href="{{ route('admin.subjects.create') }}" style="color: var(--color-primary);">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
