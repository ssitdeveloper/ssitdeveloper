@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; gap: var(--spacing-3); align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;">Activity Logs</h2>
        <form method="POST" action="{{ route('admin.activity-logs.clear') }}" style="display: inline;" onsubmit="return confirm('Clear logs older than 90 days?');">
            @csrf
            <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: #dc3545; color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                Clear Old Logs
            </button>
        </form>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                <tr>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">User</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Action</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Model</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Changes</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Date</th>
                    <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid var(--color-gray-200);">
                        <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">{{ $log->user->name ?? 'System' }}</td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">
                            <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color:
                                @if($log->action === 'created') #d4edda
                                @elseif($log->action === 'updated') #fff3cd
                                @elseif($log->action === 'deleted') #f8d7da
                                @else var(--color-gray-100)
                                @endif
                            ; color:
                                @if($log->action === 'created') #155724
                                @elseif($log->action === 'updated') #856404
                                @elseif($log->action === 'deleted') #721c24
                                @else var(--color-gray-700)
                                @endif
                            ; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold);">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ class_basename($log->model_type) }}</td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700); font-size: var(--font-size-sm);">
                            <a href="{{ route('admin.activity-logs.show', $log) }}" style="color: var(--color-primary); text-decoration: none;">View Changes</a>
                        </td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700); font-size: var(--font-size-sm);">{{ $log->created_at->diffForHumans() }}</td>
                        <td style="padding: var(--spacing-2); text-align: center;">
                            <a href="{{ route('admin.activity-logs.show', $log) }}" style="color: var(--color-primary); text-decoration: none;">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: var(--spacing-4); text-align: center; color: var(--color-gray-500);">No activity logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
