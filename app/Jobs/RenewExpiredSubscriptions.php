<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\Payment;
use App\Models\User;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Mail\SubscriptionExpiringMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RenewExpiredSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job
     *
     * Finds subscriptions expiring in 3 days with auto_renew enabled
     * and attempts to charge them using saved payment methods
     */
    public function handle(): void
    {
        // Find subscriptions expiring in 3 days with auto_renew enabled
        $subscriptions = Subscription::where('auto_renew', true)
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->whereDate('expires_at', '=', now()->addDays(3)->toDateString())
            ->with('user')
            ->get();

        Log::info("RenewExpiredSubscriptions job found {$subscriptions->count()} subscriptions to renew");

        foreach ($subscriptions as $subscription) {
            try {
                $this->renewSubscription($subscription);
            } catch (\Exception $e) {
                Log::error("Failed to renew subscription {$subscription->id}", [
                    'user_id' => $subscription->user_id,
                    'error' => $e->getMessage(),
                ]);

                // Send failure notification to user
                $this->notifyRenewalFailure($subscription);
            }
        }
    }

    /**
     * Attempt to renew a subscription
     */
    private function renewSubscription(Subscription $subscription): void
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        // Determine plan price (in smallest currency unit)
        $priceMap = [
            'BASIC' => 29900,      // $299 for 3 months
            'PREMIUM' => 49900,    // $499 for 3 months
            'PRO' => 79900,        // $799 for 3 months
        ];

        $amount = $priceMap[$plan->value] ?? 29900;
        $gateway = $subscription->payment_gateway ?? 'stripe'; // Default to Stripe

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'currency' => 'USD',
            'payment_gateway' => $gateway,
            'status' => PaymentStatus::PENDING->value,
            'description' => "Auto-renewal: {$plan->value} Plan",
        ]);

        try {
            // Attempt payment based on gateway
            if ($gateway === 'stripe') {
                $this->chargeViaStripe($user, $subscription, $payment, $amount);
            } elseif ($gateway === 'razorpay') {
                $this->chargeViaRazorpay($user, $subscription, $payment, $amount);
            }

            Log::info("Subscription renewal successful", [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'user_id' => $user->id,
            ]);

        } catch (\Exception $e) {
            $payment->update([
                'status' => PaymentStatus::FAILED->value,
                'failure_reason' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Charge via Stripe using saved card
     */
    private function chargeViaStripe(User $user, Subscription $subscription, Payment $payment, int $amount): void
    {
        // Get Stripe customer ID from user
        $stripe_customer_id = $user->stripe_customer_id;

        if (!$stripe_customer_id) {
            throw new \Exception('No Stripe customer ID found for user');
        }

        // Get the user's default payment method
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $customer = \Stripe\Customer::retrieve($stripe_customer_id);
        $payment_method = $customer->invoice_settings->default_payment_method;

        if (!$payment_method) {
            throw new \Exception('No default payment method on file');
        }

        try {
            // Create payment intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $stripe_customer_id,
                'payment_method' => $payment_method,
                'off_session' => true,
                'confirm' => true,
                'description' => $payment->description,
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                ],
            ]);

            // Update payment record
            $payment->update([
                'status' => PaymentStatus::COMPLETED->value,
                'gateway_transaction_id' => $paymentIntent->id,
                'completed_at' => now(),
            ]);

            // Extend subscription
            $subscription->update([
                'expires_at' => $subscription->expires_at->addMonths($subscription->duration_months ?? 1),
                'auto_renew' => true,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception("Stripe error: " . $e->getMessage());
        }
    }

    /**
     * Charge via Razorpay using saved card
     */
    private function chargeViaRazorpay(User $user, Subscription $subscription, Payment $payment, int $amount): void
    {
        // Get Razorpay customer ID from user
        $razorpay_customer_id = $user->razorpay_customer_id;

        if (!$razorpay_customer_id) {
            throw new \Exception('No Razorpay customer ID found for user');
        }

        try {
            $razorpay = new \Razorpay\Api\Api(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            // Get customer's saved tokens
            $customer = $razorpay->customer->fetch($razorpay_customer_id);
            $tokens = $customer->tokens()->get();

            if (empty($tokens['items'])) {
                throw new \Exception('No saved payment methods found');
            }

            // Use first saved token
            $token = $tokens['items'][0];

            // Create payment with saved token
            $paymentDetails = $razorpay->payment->create([
                'customer_id' => $razorpay_customer_id,
                'token' => $token['id'],
                'recurring' => '1',
                'email' => $user->email,
                'contact' => $user->phone ?? '',
                'amount' => $amount,
                'currency' => 'INR',
                'description' => $payment->description,
                'notes' => [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                ],
            ]);

            // Update payment record
            $payment->update([
                'status' => PaymentStatus::COMPLETED->value,
                'gateway_transaction_id' => $paymentDetails['id'],
                'completed_at' => now(),
            ]);

            // Extend subscription
            $subscription->update([
                'expires_at' => $subscription->expires_at->addMonths($subscription->duration_months ?? 1),
                'auto_renew' => true,
            ]);

        } catch (\Exception $e) {
            throw new \Exception("Razorpay error: " . $e->getMessage());
        }
    }

    /**
     * Notify user of renewal failure
     */
    private function notifyRenewalFailure(Subscription $subscription): void
    {
        try {
            Mail::to($subscription->user->email)->send(
                new SubscriptionExpiringMail($subscription)
            );
        } catch (\Exception $e) {
            Log::error("Failed to send renewal failure email", [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
