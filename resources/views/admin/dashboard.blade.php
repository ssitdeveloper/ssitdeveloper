@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Statistics Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-label">Total Users</div>
            <div class="stat-card-value">{{ number_format($totalUsers) }}</div>
            <div class="stat-card-change">{{ $totalStudents }} students active</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-label">Active Subscriptions</div>
            <div class="stat-card-value">{{ number_format($activeSubscriptions) }}</div>
            <div class="stat-card-change">Current active plans</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-label">Total Revenue</div>
            <div class="stat-card-value">₹{{ number_format($totalRevenue, 0) }}</div>
            <div class="stat-card-change">All time earnings</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-label">Monthly Revenue</div>
            <div class="stat-card-value">₹{{ number_format($monthlyRevenue, 0) }}</div>
            <div class="stat-card-change">{{ $newStudents }} new students</div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-3); margin-bottom: var(--spacing-4);">
        <!-- Recent Payments -->
        <div class="admin-table">
            <div class="admin-table-header">
                <h2>Recent Payments</h2>
                <a href="{{ route('admin.payments.index') }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold); font-size: var(--font-size-sm);">View All →</a>
            </div>
            <div class="admin-table-body">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Plan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->name }}</td>
                            <td>₹{{ number_format($payment->amount, 0) }}</td>
                            <td>{{ $payment->plan_name }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($payment->status) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--color-gray-400);">No payments yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="admin-table">
            <div class="admin-table-header">
                <h2>Active Subscriptions</h2>
                <a href="{{ route('admin.subscriptions.index') }}" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold); font-size: var(--font-size-sm);">View All →</a>
            </div>
            <div class="admin-table-body">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Expires</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Subscription::with('user')->where('status', 'active')->limit(5)->get() as $sub)
                        <tr>
                            <td>{{ $sub->user->name }}</td>
                            <td>{{ ucfirst($sub->plan->value) }}</td>
                            <td>{{ $sub->expires_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ $sub->expires_at->isPast() ? 'expired' : 'active' }}">
                                    {{ $sub->expires_at->isPast() ? 'Expired' : 'Active' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--color-gray-400);">No active subscriptions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-3);">
        <a href="{{ route('admin.users.index') }}" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl); padding: var(--spacing-3); text-decoration: none; transition: all var(--transition-fast); text-align: center;" onmouseover="this.style.boxShadow='var(--shadow-lg)'; this.style.borderColor='var(--color-primary)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='var(--color-gray-200)'">
            <div style="font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Manage Users</div>
            <div style="font-size: var(--font-size-xs); color: var(--color-gray-600); margin-top: var(--spacing-1);">View & edit user accounts</div>
        </a>

        <a href="{{ route('admin.questions.index') }}" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl); padding: var(--spacing-3); text-decoration: none; transition: all var(--transition-fast); text-align: center;" onmouseover="this.style.boxShadow='var(--shadow-lg)'; this.style.borderColor='var(--color-primary)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='var(--color-gray-200)'">
            <div style="font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Question Bank</div>
            <div style="font-size: var(--font-size-xs); color: var(--color-gray-600); margin-top: var(--spacing-1);">Manage questions & topics</div>
        </a>

        <a href="{{ route('admin.tests.index') }}" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl); padding: var(--spacing-3); text-decoration: none; transition: all var(--transition-fast); text-align: center;" onmouseover="this.style.boxShadow='var(--shadow-lg)'; this.style.borderColor='var(--color-primary)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='var(--color-gray-200)'">
            <div style="font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Test Management</div>
            <div style="font-size: var(--font-size-xs); color: var(--color-gray-600); margin-top: var(--spacing-1);">Create & manage tests</div>
        </a>

        <a href="{{ route('admin.subscriptions.index') }}" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl); padding: var(--spacing-3); text-decoration: none; transition: all var(--transition-fast); text-align: center;" onmouseover="this.style.boxShadow='var(--shadow-lg)'; this.style.borderColor='var(--color-primary)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='var(--color-gray-200)'">
            <div style="font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Subscriptions</div>
            <div style="font-size: var(--font-size-xs); color: var(--color-gray-600); margin-top: var(--spacing-1);">View & manage plans</div>
        </a>

        <a href="{{ route('admin.payments.index') }}" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl); padding: var(--spacing-3); text-decoration: none; transition: all var(--transition-fast); text-align: center;" onmouseover="this.style.boxShadow='var(--shadow-lg)'; this.style.borderColor='var(--color-primary)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='var(--color-gray-200)'">
            <div style="font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Payments</div>
            <div style="font-size: var(--font-size-xs); color: var(--color-gray-600); margin-top: var(--spacing-1);">View transaction history</div>
        </a>

        <a href="{{ route('admin.subjects.index') }}" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-xl); padding: var(--spacing-3); text-decoration: none; transition: all var(--transition-fast); text-align: center;" onmouseover="this.style.boxShadow='var(--shadow-lg)'; this.style.borderColor='var(--color-primary)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='var(--color-gray-200)'">
            <div style="font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Subjects</div>
            <div style="font-size: var(--font-size-xs); color: var(--color-gray-600); margin-top: var(--spacing-1);">Manage course subjects</div>
        </a>
    </div>
@endsection
