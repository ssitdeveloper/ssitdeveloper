@extends('layouts.admin')

@section('title', 'Create Invoice')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.invoices.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Invoices</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); max-width: 600px;">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Create New Invoice</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <ul style="margin: 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.invoices.store') }}" style="display: grid; gap: var(--spacing-4);">
            @csrf

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Payment <span style="color: #dc2626;">*</span></label>
                <select name="payment_id" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <option value="">Select Payment</option>
                    @foreach($payments as $payment)
                        <option value="{{ $payment->id }}">{{ $payment->transaction_id }} - ₹{{ number_format($payment->amount, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Invoice Number <span style="color: #dc2626;">*</span></label>
                <input type="text" name="invoice_number" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;" placeholder="e.g., INV-001">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Invoice Date <span style="color: #dc2626;">*</span></label>
                <input type="datetime-local" name="invoice_date" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Amount <span style="color: #dc2626;">*</span></label>
                <input type="number" name="amount" step="0.01" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Status <span style="color: #dc2626;">*</span></label>
                <select name="status" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box;">
                    <option value="draft">Draft</option>
                    <option value="issued" selected>Issued</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium);">Notes</label>
                <textarea name="notes" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); box-sizing: border-box; min-height: 100px;"></textarea>
            </div>

            <div style="display: flex; gap: var(--spacing-2);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Create</button>
                <a href="{{ route('admin.invoices.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
