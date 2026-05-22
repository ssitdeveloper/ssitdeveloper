@extends('layouts.admin')

@section('title', 'Manage Questions')

@section('content')
    <div style="margin-bottom: var(--spacing-4); display: flex; gap: var(--spacing-3); align-items: center;">
        <h2 style="margin: 0; flex: 1;">Manage Questions</h2>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">+ Add New Question</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4);">
            {{ session('success') }}
        </div>
    @endif

    <!-- Questions Table -->
    <div class="admin-table" style="background: white; border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--color-gray-100); border-bottom: 1px solid var(--color-gray-300);">
                <tr>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Question</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Subject</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Difficulty</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Status</th>
                    <th style="padding: var(--spacing-3); text-align: center; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr style="border-bottom: 1px solid var(--color-gray-200); transition: background 0.2s;">
                        <td style="padding: var(--spacing-3);">#{{ $question->id }}</td>
                        <td style="padding: var(--spacing-3); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ substr($question->question_text, 0, 60) }}...
                        </td>
                        <td style="padding: var(--spacing-3);">
                            {{ $question->chapter?->topic?->subject?->name ?? 'N/A' }}
                        </td>
                        <td style="padding: var(--spacing-3);">
                            <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background: var(--color-gray-200); border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
                                {{ $question->difficulty_level ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="padding: var(--spacing-3);">
                            <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background: {{ $question->is_published ? '#d4edda' : '#f8d7da' }}; color: {{ $question->is_published ? '#155724' : '#721c24' }}; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
                                {{ $question->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td style="padding: var(--spacing-3); text-align: center;">
                            <a href="{{ route('admin.questions.edit', $question) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">Edit</a>
                            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" style="display: inline;" onsubmit="return confirm('Delete this question?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: var(--spacing-4); text-align: center; color: var(--color-gray-500);">
                            No questions found. <a href="{{ route('admin.questions.create') }}" style="color: var(--color-primary);">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($questions->hasPages())
        <div style="margin-top: var(--spacing-4); display: flex; justify-content: center; gap: var(--spacing-2);">
            {{ $questions->links() }}
        </div>
    @endif
@endsection
