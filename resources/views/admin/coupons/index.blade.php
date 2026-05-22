@extends('layouts.admin')

@section('title', 'Manage Coupons')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; gap: var(--spacing-3); align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;">Manage Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block;">
            + Create Coupon
        </a>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4); border-left: 4px solid #059669;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                <tr>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Code</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Discount</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Valid Until</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Usage</th>
                    <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                    <th style="padding: var(--spacing-2); text-align: center; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr style="border-bottom: 1px solid var(--color-gray-200);">
                        <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-bold);">{{ $coupon->code }}</td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">
                            @if($coupon->discount_type === 'percentage')
                                {{ $coupon->discount_value }}%
                            @else
                                ₹{{ $coupon->discount_value }}
                            @endif
                        </td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : 'N/A' }}</td>
                        <td style="padding: var(--spacing-2); color: var(--color-gray-700);">{{ $coupon->usage_count }}/{{ $coupon->max_usage ?? '∞' }}</td>
                        <td style="padding: var(--spacing-2);">
                            @if($coupon->is_active)
                                <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #d4edda; color: #155724; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Active</span>
                            @else
                                <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #f8d7da; color: #721c24; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Inactive</span>
                            @endif
                        </td>
                        <td style="padding: var(--spacing-2); text-align: center;">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" style="color: var(--color-primary); text-decoration: none; margin-right: var(--spacing-2);">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" style="display: inline;" onsubmit="return confirm('Delete this coupon?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: var(--spacing-4); text-align: center; color: var(--color-gray-500);">No coupons found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons->hasPages())
        <div style="margin-top: var(--spacing-4); display: flex; justify-content: center;">
            {{ $coupons->links() }}
        </div>
    @endif
</div>
@endsection
