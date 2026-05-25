<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\Payment;
use App\Models\Subscription;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class RazorpayWebhookController extends Controller
{
    /**
     * Handle Razorpay webhook events
     *
     * Webhook events handled:
     * - payment.authorized (Payment authorized)
     * - payment.failed (Payment failed)
     * - refund.completed (Refund completed)
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $webhook_secret = config('services.razorpay.webhook_secret');

        // Verify Razorpay signature
        if (!$this->verifySignature($payload, $signature, $webhook_secret)) {
            return response()->json(['error' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        $event = json_decode($payload, true);
        $eventType = $event['event'] ?? null;
        $data = $event['payload'] ?? [];

        // Handle different event types
        match ($eventType) {
            'payment.authorized' => $this->handlePaymentAuthorized($data),
            'payment.failed' => $this->handlePaymentFailed($data),
            'refund.completed' => $this->handleRefundCompleted($data),
            default => null,
        };

        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }

    /**
     * Verify Razorpay webhook signature
     */
    private function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $generated_signature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($generated_signature, $signature);
    }

    /**
     * Handle authorized payment (payment.authorized)
     */
    private function handlePaymentAuthorized(array $data)
    {
        $payment_data = $data['payment'] ?? [];
        $razorpay_payment_id = $payment_data['id'] ?? null;

        if (!$razorpay_payment_id) {
            return;
        }

        // Find payment by Razorpay payment ID
        $payment = Payment::where('gateway_transaction_id', $razorpay_payment_id)->first();

        if (!$payment) {
            \Log::warning('Razorpay payment.authorized event for unknown payment', [
                'razorpay_payment_id' => $razorpay_payment_id,
                'amount' => $payment_data['amount'] ?? 0,
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

                \Log::info('Subscription activated via Razorpay payment', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'payment_id' => $payment->id,
                ]);
            }
        }

        // Capture payment (convert from authorized to captured)
        try {
            $razorpay_service = app('razorpay');
            $razorpay_service->payment->fetch($razorpay_payment_id)->capture(
                $payment_data['amount']
            );
        } catch (\Exception $e) {
            \Log::error('Failed to capture Razorpay payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed payment (payment.failed)
     */
    private function handlePaymentFailed(array $data)
    {
        $payment_data = $data['payment'] ?? [];
        $razorpay_payment_id = $payment_data['id'] ?? null;

        if (!$razorpay_payment_id) {
            return;
        }

        $payment = Payment::where('gateway_transaction_id', $razorpay_payment_id)->first();

        if (!$payment) {
            return;
        }

        // Update payment status to failed
        $payment->update([
            'status' => PaymentStatus::FAILED->value,
            'failure_reason' => $payment_data['description'] ?? 'Payment failed',
        ]);

        \Log::error('Razorpay payment failed', [
            'payment_id' => $payment->id,
            'razorpay_payment_id' => $razorpay_payment_id,
            'error' => $payment_data['description'] ?? 'Unknown error',
        ]);

        // TODO: Send failure email to user with retry instructions
    }

    /**
     * Handle refund completed (refund.completed)
     */
    private function handleRefundCompleted(array $data)
    {
        $refund_data = $data['refund'] ?? [];
        $razorpay_payment_id = $refund_data['payment_id'] ?? null;

        if (!$razorpay_payment_id) {
            return;
        }

        $payment = Payment::where('gateway_transaction_id', $razorpay_payment_id)->first();

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

        \Log::info('Payment refunded via Razorpay', [
            'payment_id' => $payment->id,
            'refund_amount' => $refund_data['amount'] ?? 0,
        ]);

        // TODO: Send refund confirmation email to user
    }
}
