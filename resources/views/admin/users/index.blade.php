@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
    <div style="margin-bottom: var(--spacing-4); display: flex; gap: var(--spacing-3); align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;">Manage Users</h2>
        <a href="{{ route('admin.users.create') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block; transition: all var(--transition-fast);">
            + Create User
        </a>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4); border-left: 4px solid #059669;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="admin-table" style="background: white; border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--color-gray-100); border-bottom: 1px solid var(--color-gray-300);">
                <tr>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Name</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Email</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Role</th>
                    <th style="padding: var(--spacing-3); text-align: left; font-weight: 600;">Status</th>
                    <th style="padding: var(--spacing-3); text-align: center; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid var(--color-gray-200); transition: background 0.2s;">
                        <td style="padding: var(--spacing-3);">#{{ $user->id }}</td>
                        <td style="padding: var(--spacing-3);">
                            {{ $user->name }}
                        </td>
                        <td style="padding: var(--spacing-3);">
                            {{ $user->email }}
                        </td>
                        <td style="padding: var(--spacing-3);">
                            <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background: var(--color-gray-200); border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
                                {{ $user->role?->label() ?? 'Student' }}
                            </span>
                        </td>
                        <td style="padding: var(--spacing-3);">
                            <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background: #d4edda; color: #155724; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">
                                Active
                            </span>
                        </td>
                        <td style="padding: var(--spacing-3); text-align: center;">
                            <a href="{{ route('admin.users.show', $user) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">View</a>
                            <a href="{{ route('admin.users.edit', $user) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: var(--spacing-4); text-align: center; color: var(--color-gray-500);">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div style="margin-top: var(--spacing-4); display: flex; justify-content: center; gap: var(--spacing-2);">
            {{ $users->links() }}
        </div>
    @endif
@endsection
