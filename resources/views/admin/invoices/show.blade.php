@extends('layouts.admin')

@section('title', $invoice->invoice_number)

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.invoices.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Invoices</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Edit</a>
            <button onclick="window.print()" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-500); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Print</button>
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: #dc2626; color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Delete</button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); margin-bottom: var(--spacing-4);">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-4); margin-bottom: var(--spacing-4); padding-bottom: var(--spacing-4); border-bottom: 2px solid var(--color-gray-200);">
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Invoice Number</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $invoice->invoice_number }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Invoice Date</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $invoice->invoice_date->format('M d, Y') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Amount</p>
                <p style="margin: 0; color: var(--color-gray-900); font-size: var(--font-size-lg); font-weight: var(--font-weight-semibold);">₹{{ number_format($invoice->amount, 2) }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Status</p>
                <p style="margin: 0;">
                    @php
                        $statusColor = match($invoice->status) {
                            'paid' => ['bg' => '#dcfce7', 'text' => '#166534'],
                            'issued' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                            'draft' => ['bg' => '#f3f4f6', 'text' => '#374151'],
                            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                            default => ['bg' => '#f3f4f6', 'text' => '#374151']
                        };
                    @endphp
                    <span style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); display: inline-block;">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </p>
            </div>
        </div>

        @if($invoice->payment)
            <div style="background-color: var(--color-gray-50); padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4);">
                <p style="margin: 0 0 var(--spacing-2) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Related Payment</p>
                <div style="display: grid; gap: var(--spacing-2);">
                    <p style="margin: 0;"><span style="font-weight: var(--font-weight-medium);">Transaction ID:</span> {{ $invoice->payment->transaction_id }}</p>
                    <p style="margin: 0;"><span style="font-weight: var(--font-weight-medium);">Amount:</span> ₹{{ number_format($invoice->payment->amount, 2) }}</p>
                    <p style="margin: 0;"><span style="font-weight: var(--font-weight-medium);">Date:</span> {{ $invoice->payment->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        @endif

        @if($invoice->notes)
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Notes</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $invoice->notes }}</p>
            </div>
        @endif

        <div style="margin-top: var(--spacing-4); padding-top: var(--spacing-4); border-top: 1px solid var(--color-gray-200);">
            <p style="margin: 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">
                Created: {{ $invoice->created_at->format('M d, Y H:i') }} |
                Updated: {{ $invoice->updated_at->format('M d, Y H:i') }}
            </p>
        </div>
    </div>
</div>
@endsection
