@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

    <!-- Payment Details -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
        <h2 style="margin: 0 0 20px 0; font-size: 18px; color: #0f172a; font-weight: 700;">Payment Information</h2>

        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 16px;">
            <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Transaction ID</p>
            <p style="margin: 0; color: #0f172a; font-size: 16px; font-weight: 600; font-family: monospace;">{{ $payment->transaction_id ?? 'N/A' }}</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 16px;">
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Amount</p>
                <p style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 700;">₹{{ number_format($payment->amount, 2) }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Currency</p>
                <p style="margin: 0; color: #0f172a; font-size: 16px; font-weight: 600;">{{ strtoupper($payment->currency) }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 16px;">
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Payment Method</p>
                <p style="margin: 0; color: #0f172a; font-size: 14px; font-weight: 600;">{{ ucfirst($payment->payment_method) }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Status</p>
                <div>
                    @if($payment->status->value === 'COMPLETED')
                        <span style="background: #ecfdf5; color: #15803d; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">✓ Completed</span>
                    @elseif($payment->status->value === 'PENDING')
                        <span style="background: #fffbeb; color: #b45309; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">⏳ Pending</span>
                    @elseif($payment->status->value === 'FAILED')
                        <span style="background: #fef2f2; color: #b91c1c; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">✗ Failed</span>
                    @else
                        <span style="background: #fef2f2; color: #b91c1c; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">↩ Refunded</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 16px;">
            <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Receipt URL</p>
            @if($payment->receipt_url)
                <a href="{{ $payment->receipt_url }}" target="_blank" style="color: #60a5fa; text-decoration: none; font-weight: 600;">View Receipt →</a>
            @else
                <p style="margin: 0; color: #94a3b8; font-size: 14px;">Not available</p>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Date Created</p>
                <p style="margin: 0; color: #0f172a; font-size: 14px;">{{ $payment->created_at->format('M d, Y • h:i A') }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Last Updated</p>
                <p style="margin: 0; color: #0f172a; font-size: 14px;">{{ $payment->updated_at->format('M d, Y • h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Related Info -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">

        <!-- User Info -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; color: #0f172a; font-weight: 700;">User Details</h3>
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase;">Name</p>
                <p style="margin: 0; color: #0f172a; font-weight: 600;">{{ $payment->user->name }}</p>
            </div>
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase;">Email</p>
                <p style="margin: 0; color: #0f172a; font-size: 13px; word-break: break-all;">{{ $payment->user->email }}</p>
            </div>
            <a href="{{ route('admin.users.show', $payment->user->id) }}" style="color: #60a5fa; text-decoration: none; font-weight: 600; transition: all 0.2s; display: block;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#60a5fa'">View User Profile →</a>
        </div>

        <!-- Subscription Info -->
        @if($payment->subscription)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; color: #0f172a; font-weight: 700;">Subscription Plan</h3>
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase;">Plan</p>
                <p style="margin: 0; color: #0f172a; font-weight: 600;">{{ $payment->subscription->plan->value }}</p>
            </div>
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase;">Status</p>
                <span style="@if($payment->subscription->isActive()) background: #ecfdf5; color: #15803d; @else background: #fef2f2; color: #b91c1c; @endif padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                    @if($payment->subscription->isActive()) ✓ Active @else ✗ Inactive @endif
                </span>
            </div>
            <div>
                <p style="margin: 0 0 4px 0; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase;">Expires</p>
                <p style="margin: 0; color: #0f172a; font-size: 13px;">{{ $payment->subscription->expires_at->format('M d, Y') }}</p>
            </div>
        </div>
        @endif

    </div>

</div>

<!-- Back Button -->
<div style="margin-top: 24px; display: flex; gap: 12px;">
    <a href="{{ route('admin.payments.index') }}" style="padding: 10px 20px; background: white; color: #60a5fa; border: 1px solid #60a5fa; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='white'">← Back to Payments</a>
</div>

@endsection
