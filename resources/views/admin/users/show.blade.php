@extends('layouts.admin')

@section('title', $user->name . ' - User Details')

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <div>
            <a href="{{ route('admin.users.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Users</a>
        </div>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary" style="text-decoration: none;">Edit User</a>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-danger); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; transition: all var(--transition-fast);">
                    Delete User
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div style="background-color: rgba(16, 185, 129, 0.1); color: #059669; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #059669;">
            {{ session('success') }}
        </div>
    @endif

    <!-- User Information -->
    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm); margin-bottom: var(--spacing-4);">
        <h2 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-gray-900);">User Information</h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-4);">
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Full Name</p>
                <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $user->name }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Email Address</p>
                <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $user->email }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Role</p>
                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: var(--font-size-xs); font-weight: var(--font-weight-semibold); background-color: rgba(44, 90, 160, 0.2); color: var(--color-primary-dark);">{{ ucfirst($user->role->value) }}</span>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Status</p>
                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: var(--font-size-xs); font-weight: var(--font-weight-semibold); background-color: {{ $user->is_active ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }}; color: {{ $user->is_active ? '#059669' : '#dc2626' }};">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Created</p>
                <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $user->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Last Login</p>
                <p style="margin: 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
            </div>
        </div>
    </div>

    <!-- Subscriptions -->
    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm); margin-bottom: var(--spacing-4);">
        <h2 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-gray-900);">Subscriptions</h2>

        @if($subscriptions->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: var(--color-gray-50); border-bottom: 1px solid var(--color-gray-200);">
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Plan</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Started</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Expires</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr style="border-bottom: 1px solid var(--color-gray-100);">
                                <td style="padding: var(--spacing-2);">{{ ucfirst($subscription->plan_name) }}</td>
                                <td style="padding: var(--spacing-2);">{{ $subscription->starts_at->format('M d, Y') }}</td>
                                <td style="padding: var(--spacing-2);">{{ $subscription->expires_at->format('M d, Y') }}</td>
                                <td style="padding: var(--spacing-2);">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: var(--font-size-xs); font-weight: var(--font-weight-semibold); background-color: rgba(16, 185, 129, 0.2); color: #059669;">
                                        {{ ucfirst($subscription->status->value) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $subscriptions->links() }}
        @else
            <p style="color: var(--color-gray-600); margin: 0;">No subscriptions yet.</p>
        @endif
    </div>

    <!-- Payments -->
    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h2 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-gray-900);">Payment History</h2>

        @if($payments->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: var(--color-gray-50); border-bottom: 1px solid var(--color-gray-200);">
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Date</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Plan</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Amount</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr style="border-bottom: 1px solid var(--color-gray-100);">
                                <td style="padding: var(--spacing-2);">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td style="padding: var(--spacing-2);">{{ $payment->plan_name }}</td>
                                <td style="padding: var(--spacing-2);">₹{{ number_format($payment->amount, 0) }}</td>
                                <td style="padding: var(--spacing-2);">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: var(--font-size-xs); font-weight: var(--font-weight-semibold); background-color: rgba(16, 185, 129, 0.2); color: #059669;">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        @else
            <p style="color: var(--color-gray-600); margin: 0;">No payments yet.</p>
        @endif
    </div>
</div>
@endsection
