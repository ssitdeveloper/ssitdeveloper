@extends('layouts.admin')

@section('title', 'Manage Invoices')

@section('content')
<div class="admin-page-container">
    <div class="page-header">
        <div>
            <h1>Invoice Management</h1>
            <p>View and manage all invoices</p>
        </div>
        <a href="{{ route('admin.invoices.create') }}" class="btn-primary">+ Create Invoice</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <strong>Success!</strong> {{ session('success') }}
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-panel">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" placeholder="Search by invoice number or customer..."
                       value="{{ request('search') }}" class="filter-input">
            </div>
            <div class="filter-group">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-input" placeholder="From Date">
            </div>
            <div class="filter-group">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-input" placeholder="To Date">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Filter</button>
                <a href="{{ route('admin.invoices.index') }}" class="btn-reset">Clear</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value">{{ $invoices->total() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">₹{{ number_format($invoices->sum('total_amount'), 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Amount</div>
            <div class="stat-value">₹{{ number_format($invoices->avg('total_amount'), 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">This Month</div>
            <div class="stat-value">₹{{ number_format($invoices->whereBetween('issued_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount'), 0) }}</div>
        </div>
    </div>

    <!-- Invoices Table -->
    @if($invoices->count() > 0)
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Invoice #</th>
                        <th style="width: 25%;">Customer</th>
                        <th style="width: 15%;">Amount</th>
                        <th style="width: 15%;">Tax</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 15%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr class="table-row">
                            <td>
                                <strong>{{ $invoice->invoice_number }}</strong>
                            </td>
                            <td>
                                {{ $invoice->payment->user->name }}<br>
                                <span class="text-muted">{{ $invoice->payment->user->email }}</span>
                            </td>
                            <td>
                                <strong>₹{{ number_format($invoice->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                ₹{{ number_format($invoice->tax_amount, 2) }}
                            </td>
                            <td>
                                {{ $invoice->issued_at->format('M d, Y') }}
                            </td>
                            <td class="action-cell">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn-action btn-view" title="View">👁️</a>
                                <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn-action btn-download" title="Download PDF">⬇️</a>
                                <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this invoice?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Delete">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $invoices->links('pagination::bootstrap-4') }}
        </div>
    @else
        <div class="empty-state">
            <p>No invoices found.</p>
            <a href="{{ route('admin.invoices.create') }}" class="btn-primary">Create your first invoice</a>
        </div>
    @endif
</div>

<style>
    .admin-page-container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .page-header p {
        color: #6b7280;
        margin: 5px 0 0 0;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
        border-left-color: #059669;
    }

    .filter-panel {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .filter-form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }

    .filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .btn-filter,
    .btn-reset {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-filter {
        background-color: #3b82f6;
        color: white;
    }

    .btn-filter:hover {
        background-color: #2563eb;
    }

    .btn-reset {
        background-color: #e5e7eb;
        color: #374151;
        text-decoration: none;
    }

    .btn-reset:hover {
        background-color: #d1d5db;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f3f4f6, #ffffff);
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }

    .stat-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 10px 0 0 0;
    }

    .table-container {
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .admin-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .admin-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
        font-size: 14px;
    }

    .admin-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .text-muted {
        color: #9ca3af;
        font-size: 0.85em;
    }

    .action-cell {
        text-align: center;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background-color: #f3f4f6;
        border: 1px solid #d1d5db;
        text-decoration: none;
        font-size: 16px;
        transition: all 0.2s;
        margin: 0 2px;
        cursor: pointer;
    }

    .btn-action:hover {
        background-color: #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
        background: #f9fafb;
        border-radius: 8px;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }
</style>
@endsection
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
        <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
