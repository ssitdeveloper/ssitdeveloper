@extends('layouts.student')

@section('title', 'Upgrade Subscription')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('student.subscription') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Subscription</a>
    </div>

    <div style="max-width: 1200px;">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-2); color: var(--color-gray-900);">Upgrade Your Subscription</h1>
        <p style="color: var(--color-gray-600); margin-bottom: var(--spacing-4);">Choose a plan and select your preferred payment method</p>

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: var(--spacing-3); padding: var(--spacing-3); background-color: #fee; border: 1px solid #fcc; border-radius: var(--radius-lg); color: var(--color-danger);">
                {{ session('error') }}
            </div>
        @endif

        @if($subscription && $subscription->status === 'active')
            <div style="margin-bottom: var(--spacing-4); padding: var(--spacing-3); background-color: #efe; border: 1px solid #cfc; border-radius: var(--radius-lg); color: var(--color-success);">
                <p style="margin: 0;">
                    <strong>Current Plan:</strong> {{ ucfirst($subscription->plan->value) }}
                    (Expires: {{ $subscription->expires_at->format('M d, Y') }})
                </p>
            </div>
        @endif

        <!-- Plans Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--spacing-4); margin-bottom: var(--spacing-4);">
            @foreach($plans as $plan)
                <div class="student-card" style="text-align: center; display: flex; flex-direction: column;">
                    <h3 style="margin-top: 0; color: var(--color-gray-900);">{{ $plan['label'] }}</h3>
                    <p style="color: var(--color-gray-600); margin: var(--spacing-1) 0; flex-grow: 1;">{{ $plan['duration_days'] }} days access</p>
                    <div style="font-size: 2rem; font-weight: var(--font-weight-bold); color: var(--color-primary); margin: var(--spacing-3) 0;">₹{{ number_format($plan['price_inr'], 0) }}<span style="font-size: 1rem; color: var(--color-gray-600);">/plan</span></div>

                    @if($subscription && $subscription->plan->value === $plan['value'] && $subscription->status === 'active')
                        <button type="button" disabled style="width: 100%; padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-300); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: not-allowed; margin-top: auto; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg style="width: 18px; height: 18px;" data-lucide="check-circle"></svg>
                            Your Current Plan
                        </button>
                    @elseif(count($configuredGateways) > 0)
                        <form action="{{ route('student.subscription.upgrade.process') }}" method="POST" style="width: 100%; margin-top: auto;">
                            @csrf

                            <input type="hidden" name="plan" value="{{ $plan['value'] }}">

                            <!-- Gateway Selection -->
                            <div style="margin-bottom: var(--spacing-2); text-align: left;">
                                <label style="display: block; font-size: 0.875rem; color: var(--color-gray-700); font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Payment Method:</label>

                                @php
                                    $gatewayNames = array_map(fn($g) => strtolower((new ReflectionClass($g))->getShortName()), $configuredGateways);
                                @endphp

                                @foreach(array_combine(array_keys($configuredGateways), $gatewayNames) as $gatewayKey => $gatewayName)
                                    @php
                                        $label = match($gatewayName) {
                                            'stripepayment' => '💳 Stripe',
                                            'paypalpayment' => '🅿️ PayPal',
                                            'stripegateway' => '💳 Stripe',
                                            'paypalgateway' => '🅿️ PayPal',
                                            default => ucfirst($gatewayName),
                                        };
                                        $value = match($gatewayName) {
                                            'stripepayment' => 'stripe',
                                            'paypalpayment' => 'paypal',
                                            'stripegateway' => 'stripe',
                                            'paypalgateway' => 'paypal',
                                            default => $gatewayName,
                                        };
                                    @endphp
                                    <label style="display: flex; align-items: center; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-md); cursor: pointer; margin-bottom: var(--spacing-1); transition: all var(--transition-fast);">
                                        <input type="radio" name="gateway" value="{{ $value }}" style="margin-right: var(--spacing-2);" required>
                                        <span style="color: var(--color-gray-700);">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <button type="submit" style="width: 100%; padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; transition: all var(--transition-fast);">
                                Proceed to Payment
                            </button>
                        </form>
                    @else
                        <div style="width: 100%; padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-600); border: none; border-radius: var(--radius-lg); font-size: 0.875rem; margin-top: auto;">
                            No payment methods available
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Payment Methods Info -->
        <div style="background-color: #f0f7ff; border: 1px solid #c7e9f7; border-radius: var(--radius-lg); padding: var(--spacing-4);">
            <h3 style="margin-top: 0; color: var(--color-primary);">💳 Secure Payment Methods</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-4);">
                <div>
                    <h4 style="margin-top: 0; color: var(--color-primary);">Stripe</h4>
                    <p style="font-size: 0.875rem; color: var(--color-gray-700); margin: 0;">
                        Secure card payments. Supports all major credit and debit cards. Instant processing and encryption.
                    </p>
                </div>
                <div>
                    <h4 style="margin-top: 0; color: var(--color-primary);">PayPal</h4>
                    <p style="font-size: 0.875rem; color: var(--color-gray-700); margin: 0;">
                        Fast and secure via PayPal. Use your PayPal account or pay with cards and bank transfers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .alert {
        padding: var(--spacing-3);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-3);
    }

    .alert-success {
        background-color: #efe;
        border: 1px solid #cfc;
        color: #060;
    }

    .alert-danger {
        background-color: #fee;
        border: 1px solid #fcc;
        color: #c00;
    }
</style>
@endsection

