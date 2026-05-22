@extends('layouts.admin')

@section('title', 'Payments Management')

@section('content')
<div style="display: grid; grid-template-columns: 1fr; gap: 24px;">

    <!-- Header -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0 0 4px 0; font-size: 20px; color: #0f172a; font-weight: 700;">All Payments</h2>
                <p style="margin: 0; color: #64748b; font-size: 14px;">Manage and track payment transactions</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <form method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" placeholder="Search user or transaction..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; width: 200px;">
                    <select name="status" style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                    <button type="submit" style="padding: 8px 16px; background: #60a5fa; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='#3b82f6'" onmouseout="this.style.background='#60a5fa'">Filter</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Total Revenue</p>
            <p style="margin: 0; color: #60a5fa; font-size: 28px; font-weight: 700;">₹{{ number_format($payments->sum('amount'), 0) }}</p>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Completed</p>
            <p style="margin: 0; color: #10b981; font-size: 28px; font-weight: 700;">{{ $payments->where('status', 'COMPLETED')->count() }}</p>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Pending</p>
            <p style="margin: 0; color: #f59e0b; font-size: 28px; font-weight: 700;">{{ $payments->where('status', 'PENDING')->count() }}</p>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Failed</p>
            <p style="margin: 0; color: #ef4444; font-size: 28px; font-weight: 700;">{{ $payments->where('status', 'FAILED')->count() }}</p>
        </div>
    </div>

    <!-- Table -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">User</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">Amount</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">Plan</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">Method</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">Status</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">Date</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #0f172a;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr style="border-bottom: 1px solid #e2e8f0; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding: 12px 16px; color: #475569;">
                            <div style="font-weight: 600;">{{ $payment->user->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $payment->user->email }}</div>
                        </td>
                        <td style="padding: 12px 16px; color: #475569; font-weight: 600;">₹{{ number_format($payment->amount, 2) }}</td>
                        <td style="padding: 12px 16px; color: #475569;">
                            @if($payment->subscription)
                                <span style="background: #f0f9ff; color: #0284c7; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">{{ $payment->subscription->plan->value }}</span>
                            @else
                                <span style="background: #f3f4f6; color: #6b7280; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">—</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; color: #475569; font-size: 12px;">{{ ucfirst($payment->payment_method) }}</td>
                        <td style="padding: 12px 16px;">
                            @if($payment->status->value === 'COMPLETED')
                                <span style="background: #ecfdf5; color: #15803d; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">✓ Completed</span>
                            @elseif($payment->status->value === 'PENDING')
                                <span style="background: #fffbeb; color: #b45309; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">⏳ Pending</span>
                            @elseif($payment->status->value === 'FAILED')
                                <span style="background: #fef2f2; color: #b91c1c; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">✗ Failed</span>
                            @else
                                <span style="background: #fef2f2; color: #b91c1c; padding: 6px 12px; border-radius: 9999py; font-size: 11px; font-weight: 700; text-transform: uppercase;">↩ Refunded</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; color: #64748b; font-size: 12px;">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td style="padding: 12px 16px;">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" style="color: #60a5fa; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#60a5fa'">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">
                            <p style="margin: 0; font-size: 14px;">No payments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($payments->hasPages())
    <div style="display: flex; justify-content: center; gap: 8px;">
        @if($payments->onFirstPage())
            <button disabled style="padding: 8px 12px; background: #e2e8f0; color: #94a3b8; border: none; border-radius: 6px; font-size: 14px; cursor: not-allowed;">← Previous</button>
        @else
            <a href="{{ $payments->previousPageUrl() }}" style="padding: 8px 12px; background: #60a5fa; color: white; border: none; border-radius: 6px; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#3b82f6'" onmouseout="this.style.background='#60a5fa'">← Previous</a>
        @endif

        @foreach($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
            @if($page == $payments->currentPage())
                <button disabled style="padding: 8px 12px; background: #60a5fa; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 700;">{{ $page }}</button>
            @else
                <a href="{{ $url }}" style="padding: 8px 12px; background: white; color: #60a5fa; border: 1px solid #60a5fa; border-radius: 6px; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='white'">{{ $page }}</a>
            @endif
        @endforeach

        @if($payments->hasMorePages())
            <a href="{{ $payments->nextPageUrl() }}" style="padding: 8px 12px; background: #60a5fa; color: white; border: none; border-radius: 6px; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#3b82f6'" onmouseout="this.style.background='#60a5fa'">Next →</a>
        @else
            <button disabled style="padding: 8px 12px; background: #e2e8f0; color: #94a3b8; border: none; border-radius: 6px; font-size: 14px; cursor: not-allowed;">Next →</button>
        @endif
    </div>
    @endif

</div>
@endsection
