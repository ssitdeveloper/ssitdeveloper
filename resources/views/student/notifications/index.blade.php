@extends('layouts.student')

@section('title', 'Notifications')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4); display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0; color: var(--color-gray-900);">Notifications</h1>
        @if($notifications->count() > 0)
            <form method="POST" action="{{ route('student.notifications.mark-all-read') }}" style="display: inline;">
                @csrf
                <button type="submit" style="padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-gray-100); color: var(--color-gray-900); border: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm); cursor: pointer;">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if ($notifications->count() > 0)
        <div style="display: grid; gap: var(--spacing-2);">
            @foreach ($notifications as $notification)
                <div class="student-card" style="background-color: {{ $notification->read_at ? 'var(--color-white)' : 'rgba(59, 130, 246, 0.05)' }}; border-left: 4px solid {{ $notification->read_at ? 'var(--color-gray-300)' : 'var(--color-primary)' }};">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-900);">{{ $notification->title ?? 'Notification' }}</h3>
                            <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-700);">{{ $notification->message ?? $notification->data['message'] ?? '' }}</p>
                            <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div style="display: flex; gap: var(--spacing-1);">
                            @if (!$notification->read_at)
                                <form method="POST" action="{{ route('student.notifications.read', $notification) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="padding: var(--spacing-1) var(--spacing-2); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm); cursor: pointer;">
                                        Mark Read
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('student.notifications.delete', $notification) }}" style="display: inline;" onsubmit="return confirm('Delete this notification?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: var(--spacing-1) var(--spacing-2); background-color: #dc3545; color: white; border: none; border-radius: var(--radius-lg); font-size: var(--font-size-sm); cursor: pointer;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="student-card" style="text-align: center; color: var(--color-gray-600);">
            <p>No notifications yet</p>
        </div>
    @endif
</div>
@endsection
