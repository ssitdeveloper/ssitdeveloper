<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\Payment;
use App\Models\Subscription;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     *
     * Webhook events handled:
     * - payment_intent.succeeded (Payment completed)
     * - payment_intent.payment_failed (Payment failed)
     * - charge.refunded (Refund processed)
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        // Handle different event types
        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event),
            'charge.refunded' => $this->handleChargeRefunded($event),
            default => null,
        };

        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded(Event $event)
    {
        $paymentIntent = $event->data->object;

        // Find payment by Stripe payment intent ID
        $payment = Payment::where('gateway_transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            // Payment not found in our system, log for investigation
            \Log::warning('Stripe payment_intent.succeeded event for unknown payment', [
                'stripe_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
            ]);
            return;
        }

        // Update payment status
        $payment->update([
            'status' => PaymentStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        // If payment is linked to subscription, activate it
        if ($payment->subscription_id) {
            $subscription = Subscription::find($payment->subscription_id);
            if ($subscription) {
                $subscription->update([
                    'status' => SubscriptionStatus::ACTIVE->value,
                    'started_at' => now(),
                    'expires_at' => now()->addMonths($subscription->duration_months ?? 1),
                ]);

                // Log subscription activation
                \Log::info('Subscription activated via Stripe payment', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'payment_id' => $payment->id,
                ]);
            }
        }
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed(Event $event)
    {
        $paymentIntent = $event->data->object;

        $payment = Payment::where('gateway_transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            return;
        }

        // Update payment status to failed
        $payment->update([
            'status' => PaymentStatus::FAILED->value,
            'failure_reason' => $paymentIntent->last_payment_error?->message ?? 'Unknown error',
        ]);

        // Log for monitoring
        \Log::error('Stripe payment failed', [
            'payment_id' => $payment->id,
            'stripe_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error?->message,
        ]);

        // TODO: Send failure email to user with retry instructions
    }

    /**
     * Handle charge refunded
     */
    private function handleChargeRefunded(Event $event)
    {
        $charge = $event->data->object;

        // Find payment by Stripe charge ID
        $payment = Payment::where('gateway_transaction_id', $charge->payment_intent)->first();

        if (!$payment) {
            return;
        }

        // Update payment status to refunded
        $payment->update([
            'status' => PaymentStatus::REFUNDED->value,
            'refunded_at' => now(),
        ]);

        // If subscription was active, mark it as cancelled
        if ($payment->subscription_id) {
            $subscription = Subscription::find($payment->subscription_id);
            if ($subscription) {
                $subscription->update([
                    'status' => SubscriptionStatus::CANCELLED->value,
                    'cancelled_at' => now(),
                ]);
            }
        }

        \Log::info('Payment refunded via Stripe', [
            'payment_id' => $payment->id,
            'refund_amount' => $charge->amount_refunded,
        ]);

        // TODO: Send refund confirmation email to user
    }
}
