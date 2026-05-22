<?php

namespace App\Services;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Enums\PaymentStatus;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Payment;
use App\Services\PaymentGateways\PaymentGatewayManager;
use Carbon\Carbon;

class SubscriptionService
{
    protected PaymentGatewayManager $gatewayManager;

    public function __construct(PaymentGatewayManager $gatewayManager)
    {
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Create a subscription
     */
    public function createSubscription(User $user, SubscriptionPlan $plan, string $transactionId = null): Subscription
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan' => $plan,
            'status' => SubscriptionStatus::ACTIVE,
            'started_at' => now(),
            'expires_at' => now()->addDays($this->getDaysForPlan($plan)),
            'auto_renew' => false,
        ]);

        return $subscription;
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(Subscription $subscription): Subscription
    {
        if ($subscription->isActive()) {
            $subscription->expires_at->addDays($this->getDaysForPlan($subscription->plan));
        } else {
            $subscription->started_at = now();
            $subscription->expires_at = now()->addDays($this->getDaysForPlan($subscription->plan));
        }

        $subscription->status = SubscriptionStatus::ACTIVE;
        $subscription->save();

        return $subscription;
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        return (bool) $subscription->update([
            'status' => SubscriptionStatus::CANCELLED,
        ]);
    }

    /**
     * Get active subscription
     */
    public function getActiveSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->where('status', SubscriptionStatus::ACTIVE)
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();
    }

    /**
     * Check expired subscriptions
     */
    public function checkExpiredSubscriptions(): void
    {
        Subscription::where('status', SubscriptionStatus::ACTIVE)
            ->where('expires_at', '<=', now())
            ->update(['status' => SubscriptionStatus::EXPIRED]);
    }

    /**
     * Get subscription pricing
     */
    public function getPlanPricing(SubscriptionPlan $plan, string $currency = 'INR'): float
    {
        $pricing = [
            'INR' => [
                'monthly' => 499,
                'quarterly' => 1299,
                'yearly' => 3999,
                'premium' => 9999,
            ],
            'USD' => [
                'monthly' => 6,
                'quarterly' => 15,
                'yearly' => 50,
                'premium' => 120,
            ],
        ];

        return $pricing[$currency][$plan->value] ?? $pricing['INR'][$plan->value];
    }

    /**
     * Initiate subscription upgrade/downgrade
     */
    public function initiateUpgrade(User $user, SubscriptionPlan $newPlan, string $gateway = 'stripe', string $currency = 'INR'): array
    {
        // Check if already on this plan
        $currentSubscription = $user->subscription;

        if ($currentSubscription && $currentSubscription->plan === $newPlan && $currentSubscription->status === 'active') {
            return [
                'success' => false,
                'message' => 'You are already on this plan.',
            ];
        }

        // Prepare payment data
        $amount = $this->getPlanPricing($newPlan, $currency);

        $paymentData = [
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'plan' => $newPlan->value,
            'description' => 'Subscription upgrade to ' . ucfirst($newPlan->value) . ' Plan',
            'return_url' => route('student.subscription'),
            'cancel_url' => route('student.subscription.upgrade'),
        ];

        // Create pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $gateway,
            'status' => PaymentStatus::PENDING,
            'metadata' => [
                'plan' => $newPlan->value,
                'type' => 'subscription_upgrade',
            ],
        ]);

        // Initiate payment with gateway
        $gatewayResponse = $this->gatewayManager->initiatePayment($paymentData, $gateway);

        if (!$gatewayResponse['success']) {
            $payment->delete();
            return $gatewayResponse;
        }

        // Store payment gateway transaction ID
        if ($gateway === 'stripe') {
            $payment->update([
                'transaction_id' => $gatewayResponse['payment_intent_id'] ?? null,
            ]);
        } elseif ($gateway === 'paypal') {
            $payment->update([
                'transaction_id' => $gatewayResponse['order_id'] ?? null,
            ]);
        }

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            ...$gatewayResponse,
        ];
    }

    /**
     * Complete subscription upgrade after payment
     */
    public function completeUpgrade(Payment $payment, SubscriptionPlan $newPlan): array
    {
        $user = $payment->user;

        // Get or create subscription
        $subscription = $user->subscription ?? new Subscription();

        // Calculate new expiration date
        $expiresAt = $subscription->expires_at && $subscription->expires_at > now()
            ? $subscription->expires_at->addDays($this->getDaysForPlan($newPlan))
            : now()->addDays($this->getDaysForPlan($newPlan));

        // Update subscription
        $subscription->fill([
            'user_id' => $user->id,
            'plan' => $newPlan,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => $expiresAt,
        ])->save();

        // Update payment
        $payment->update([
            'subscription_id' => $subscription->id,
            'status' => PaymentStatus::COMPLETED,
        ]);

        return [
            'success' => true,
            'message' => 'Subscription upgraded successfully!',
            'subscription' => $subscription,
        ];
    }

    /**
     * Handle failed payment
     */
    public function handleFailedPayment(Payment $payment): array
    {
        $payment->update([
            'status' => PaymentStatus::FAILED,
        ]);

        return [
            'success' => false,
            'message' => 'Payment failed. Please try again.',
        ];
    }

    /**
     * Get days for plan duration
     */
    private function getDaysForPlan(SubscriptionPlan $plan): int
    {
        return match ($plan->value) {
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
            'premium' => 365,
            default => 30,
        };
    }

    /**
     * Get available plans
     */
    public function getAvailablePlans(): array
    {
        return array_map(function ($plan) {
            return [
                'value' => $plan->value,
                'label' => ucfirst($plan->value),
                'price_inr' => $this->getPlanPricing($plan, 'INR'),
                'price_usd' => $this->getPlanPricing($plan, 'USD'),
                'duration_days' => $this->getDaysForPlan($plan),
            ];
        }, SubscriptionPlan::cases());
    }

    /**
     * Check if upgrade is available
     */
    public function canUpgrade(User $user, SubscriptionPlan $newPlan): bool
    {
        $current = $user->subscription;

        if (!$current) {
            return true;
        }

        if ($current->plan === $newPlan && $current->status === 'active') {
            return false;
        }

        return true;
    }
}
