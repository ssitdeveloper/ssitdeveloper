@extends('layouts.student')

@section('title', 'Payment History')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <h1 style="margin: 0; color: var(--color-gray-900);">Payment History</h1>
        <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600);">View all your subscription payments and transactions.</p>
    </div>

    @if ($payments->isEmpty())
        <div class="student-card" style="text-align: center; padding: var(--spacing-8);">
            <p style="margin: 0; color: var(--color-gray-500);">No payments found. Your payment history will appear here.</p>
        </div>
    @else
        <div class="student-card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-gray-200); background-color: var(--color-gray-50);">
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Date</th>
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Amount</th>
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Plan</th>
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Status</th>
                            <th style="text-align: left; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Transaction ID</th>
                            <th style="text-align: center; padding: var(--spacing-3); color: var(--color-gray-700); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr style="border-bottom: 1px solid var(--color-gray-100); transition: background-color var(--transition-fast);">
                                <td style="padding: var(--spacing-3); color: var(--color-gray-900);">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td style="padding: var(--spacing-3); color: var(--color-gray-900); font-weight: var(--font-weight-medium);">₹{{ number_format($payment->amount, 2) }}</td>
                                <td style="padding: var(--spacing-3); color: var(--color-gray-700);">{{ optional($payment->subscription)->plan ?? 'N/A' }}</td>
                                <td style="padding: var(--spacing-3);">
                                    @php
                                        $statusColor = match($payment->status) {
                                            'completed' => ['bg' => '#dcfce7', 'text' => '#166534'],
                                            'pending' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                            'failed' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                            default => ['bg' => '#f3f4f6', 'text' => '#374151']
                                        };
                                    @endphp
                                    <span style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--radius-lg); font-size: var(--font-size-xs); font-weight: var(--font-weight-medium); display: inline-block;">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td style="padding: var(--spacing-3); color: var(--color-gray-600); font-size: var(--font-size-sm);">{{ $payment->transaction_id }}</td>
                                <td style="padding: var(--spacing-3); text-align: center;">
                                    @if ($payment->receipt_url)
                                        <a href="{{ $payment->receipt_url }}" target="_blank" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            Download
                                        </a>
                                    @else
                                        <span style="color: var(--color-gray-400); font-size: var(--font-size-sm);">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div style="margin-top: var(--spacing-4); padding-top: var(--spacing-4); border-top: 1px solid var(--color-gray-200);">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<style>
.student-card {
    background-color: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-4);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

table tbody tr:hover {
    background-color: var(--color-gray-50);
}
</style>
@endsection
