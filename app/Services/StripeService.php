<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret'));
    }

    public function createPaymentIntent(float $amount, string $currency = 'inr', array $meta = []): array
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount' => (int) ($amount * 100),
            'currency' => $currency,
            'metadata' => $meta,
        ]);

        return $intent->toArray();
    }

    public function confirmPaymentIntent(string $intentId, string $paymentMethod): array
    {
        $intent = $this->stripe->paymentIntents->confirm(
            $intentId,
            ['payment_method' => $paymentMethod]
        );

        return $intent->toArray();
    }

    public function getPaymentIntent(string $intentId): array
    {
        $intent = $this->stripe->paymentIntents->retrieve($intentId);

        return $intent->toArray();
    }

    public function createRefund(string $paymentIntentId, float $amount = null): array
    {
        $refund = $this->stripe->refunds->create([
            'payment_intent' => $paymentIntentId,
            'amount' => $amount ? (int) ($amount * 100) : null,
        ]);

        return $refund->toArray();
    }
}
