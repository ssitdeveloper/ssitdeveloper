<?php

namespace App\Services\PaymentGateways;

use App\Models\Payment;
use Illuminate\Support\Str;

class StripeGateway implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $webhookSecret;

    public function __construct()
    {
        $this->apiKey = config('payment.stripe.secret_key', '');
        $this->webhookSecret = config('payment.stripe.webhook_secret', '');
    }

    /**
     * Initialize a payment (create a payment intent)
     */
    public function initiatePayment(array $data): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Stripe is not configured. Please add your API keys.',
                'client_secret' => null,
            ];
        }

        try {
            $stripe = new \Stripe\StripeClient($this->apiKey);

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int) ($data['amount'] * 100), // Convert to cents
                'currency' => strtolower($data['currency'] ?? 'inr'),
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                    'subscription_id' => $data['subscription_id'] ?? null,
                    'order_id' => $data['order_id'] ?? Str::uuid(),
                    'description' => $data['description'] ?? 'Subscription Payment',
                ],
                'description' => $data['description'] ?? 'Subscription Payment',
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'INR',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to initiate payment: ' . $e->getMessage(),
                'client_secret' => null,
            ];
        }
    }

    /**
     * Capture/confirm a payment
     */
    public function capturePayment(string $paymentIntentId): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Stripe is not configured',
            ];
        }

        try {
            $stripe = new \Stripe\StripeClient($this->apiKey);

            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'transaction_id' => $paymentIntent->id,
                    'charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'status' => 'completed',
                ];
            }

            return [
                'success' => false,
                'status' => $paymentIntent->status,
                'message' => 'Payment not completed. Status: ' . $paymentIntent->status,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to capture payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Refund a payment
     */
    public function refund(string $chargeId, ?float $amount = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Stripe is not configured',
            ];
        }

        try {
            $stripe = new \Stripe\StripeClient($this->apiKey);

            $refund = $stripe->refunds->create([
                'charge' => $chargeId,
                'amount' => $amount ? (int) ($amount * 100) : null,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to refund payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentIntentId): string
    {
        if (!$this->isConfigured()) {
            return 'unknown';
        }

        try {
            $stripe = new \Stripe\StripeClient($this->apiKey);
            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            return match ($paymentIntent->status) {
                'succeeded' => 'completed',
                'processing' => 'processing',
                'requires_action' => 'requires_action',
                'requires_payment_method' => 'requires_payment_method',
                'canceled' => 'cancelled',
                default => $paymentIntent->status,
            };
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhook(array $data, string $signature): bool
    {
        if (!$this->webhookSecret) {
            return false;
        }

        try {
            \Stripe\Webhook::constructEvent(
                json_encode($data),
                $signature,
                $this->webhookSecret
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'] ?? null;

        match ($event) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($payload['data']['object']),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($payload['data']['object']),
            'charge.refunded' => $this->handleRefund($payload['data']['object']),
            default => null,
        };
    }

    private function handlePaymentSucceeded(object $paymentIntent): void
    {
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'completed',
            ]);

            // Activate subscription if payment is for subscription
            if ($payment->subscription) {
                $payment->subscription->update([
                    'status' => 'active',
                ]);
            }
        }
    }

    private function handlePaymentFailed(object $paymentIntent): void
    {
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
            ]);
        }
    }

    private function handleRefund(object $charge): void
    {
        $payment = Payment::where('transaction_id', $charge->payment_intent)->first();

        if ($payment) {
            $payment->update([
                'status' => 'refunded',
            ]);
        }
    }

    public function getName(): string
    {
        return 'Stripe';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
