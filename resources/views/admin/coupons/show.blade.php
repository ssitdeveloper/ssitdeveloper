@extends('layouts.admin')

@section('title', $coupon->code)

@section('content')
<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.coupons.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Coupons</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold);">Edit</a>
            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: #dc2626; color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">Delete</button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); margin-bottom: var(--spacing-4);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">{{ $coupon->code }}</h1>

        <div style="display: grid; gap: var(--spacing-3);">
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Discount</p>
                <p style="margin: 0; color: var(--color-gray-900);">
                    {{ $coupon->discount_value }}
                    @if($coupon->discount_type === 'percentage')
                        %
                    @else
                        ₹
                    @endif
                </p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Discount Type</p>
                <p style="margin: 0; color: var(--color-gray-900);">
                    {{ $coupon->discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}
                </p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Usage Limit</p>
                <p style="margin: 0; color: var(--color-gray-900);">
                    @if($coupon->usage_limit)
                        {{ $coupon->usage_limit }}
                    @else
                        Unlimited
                    @endif
                </p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Times Used</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $coupon->usage_count ?? 0 }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Valid From</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $coupon->valid_from->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Valid Until</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $coupon->valid_until->format('M d, Y H:i') }}</p>
            </div>

            @if($coupon->description)
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Description</p>
                    <p style="margin: 0; color: var(--color-gray-900);">{{ $coupon->description }}</p>
                </div>
            @endif

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Status</p>
                <p style="margin: 0;">
                    @php
                        $isActive = now() >= $coupon->valid_from && now() <= $coupon->valid_until;
                        $isExhausted = $coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit;
                    @endphp
                    <span style="background-color: {{ ($isActive && !$isExhausted) ? '#dcfce7' : '#fee2e2' }}; color: {{ ($isActive && !$isExhausted) ? '#166534' : '#991b1b' }}; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: var(--font-weight-medium); display: inline-block;">
                        @if($isExhausted)
                            Exhausted
                        @elseif($isActive)
                            Active
                        @else
                            Inactive
                        @endif
                    </span>
                </p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Created At</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $coupon->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; font-weight: var(--font-weight-semibold); color: var(--color-gray-700);">Last Updated</p>
                <p style="margin: 0; color: var(--color-gray-900);">{{ $coupon->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
