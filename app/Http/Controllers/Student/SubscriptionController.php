<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Payment;
use App\Enums\SubscriptionPlan;
use App\Services\SubscriptionService;
use App\Services\PaymentGateways\PaymentGatewayManager;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected PaymentGatewayManager $gatewayManager;

    public function __construct(SubscriptionService $subscriptionService, PaymentGatewayManager $gatewayManager)
    {
        $this->subscriptionService = $subscriptionService;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Show subscription page
     */
    public function show()
    {
        $user = auth()->user();
        $subscription = $user->subscription;
        $plans = $this->subscriptionService->getAvailablePlans();

        return view('student.subscription', compact('subscription', 'plans'));
    }

    /**
     * Show upgrade page with all available plans
     */
    public function upgrade()
    {
        $user = auth()->user();
        $subscription = $user->subscription;
        $plans = $this->subscriptionService->getAvailablePlans();
        $configuredGateways = $this->gatewayManager->configuredGateways();
        $allGateways = $this->gatewayManager->allGateways();

        // Add gateway info to plans
        foreach ($plans as &$plan) {
            $plan['available_gateways'] = array_keys($allGateways);
            $plan['configured_gateways'] = array_keys($configuredGateways);
        }

        return view('student.subscription-upgrade', compact('subscription', 'plans', 'configuredGateways'));
    }

    /**
     * Process subscription upgrade/downgrade
     */
    public function processUpgrade(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:monthly,quarterly,yearly,premium',
            'gateway' => 'required|in:stripe,paypal',
        ]);

        $user = auth()->user();
        $newPlan = SubscriptionPlan::from($validated['plan']);
        $gateway = $validated['gateway'];

        // Check if gateway is configured
        if (!$this->gatewayManager->gateway($gateway)->isConfigured()) {
            return redirect()->route('student.subscription.upgrade')
                ->with('error', ucfirst($gateway) . ' is not configured. Please try another payment method.');
        }

        // Initiate payment
        $paymentResult = $this->subscriptionService->initiateUpgrade(
            $user,
            $newPlan,
            $gateway,
            'INR'
        );

        if (!$paymentResult['success']) {
            return redirect()->route('student.subscription.upgrade')
                ->with('error', $paymentResult['message'] ?? 'Failed to initiate payment.');
        }

        // Return gateway-specific response
        if ($gateway === 'stripe') {
            return view('student.payment.stripe-checkout', [
                'clientSecret' => $paymentResult['client_secret'],
                'paymentId' => $paymentResult['payment_id'],
                'amount' => $paymentResult['amount'],
                'currency' => $paymentResult['currency'],
            ]);
        } elseif ($gateway === 'paypal') {
            return redirect($paymentResult['approval_url']);
        }

        return redirect()->route('student.subscription.upgrade')
            ->with('error', 'Invalid payment gateway.');
    }

    /**
     * Handle Stripe callback
     */
    public function stripeCallback(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::findOrFail($validated['payment_id']);
        $user = auth()->user();

        if ($payment->user_id !== $user->id) {
            return redirect()->route('student.subscription')
                ->with('error', 'Unauthorized access.');
        }

        // Get plan from payment metadata
        $plan = SubscriptionPlan::from($payment->metadata['plan'] ?? 'monthly');

        // Complete the upgrade
        $result = $this->subscriptionService->completeUpgrade($payment, $plan);

        if ($result['success']) {
            return redirect()->route('student.subscription')
                ->with('success', $result['message']);
        }

        return redirect()->route('student.subscription.upgrade')
            ->with('error', 'Failed to complete subscription upgrade.');
    }

    /**
     * Handle PayPal callback
     */
    public function paypalCallback(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
        ]);

        // Find payment by token
        $payment = Payment::where('transaction_id', $validated['token'])->first();

        if (!$payment) {
            return redirect()->route('student.subscription')
                ->with('error', 'Payment not found.');
        }

        $user = auth()->user();
        if ($payment->user_id !== $user->id) {
            return redirect()->route('student.subscription')
                ->with('error', 'Unauthorized access.');
        }

        // Capture payment
        $captureResult = $this->gatewayManager->capturePayment($validated['token'], 'paypal');

        if (!$captureResult['success']) {
            $this->subscriptionService->handleFailedPayment($payment);
            return redirect()->route('student.subscription')
                ->with('error', 'Payment capture failed: ' . ($captureResult['message'] ?? 'Unknown error'));
        }

        // Update payment with transaction ID
        $payment->update([
            'transaction_id' => $captureResult['transaction_id'],
        ]);

        // Get plan from payment metadata
        $plan = SubscriptionPlan::from($payment->metadata['plan'] ?? 'monthly');

        // Complete the upgrade
        $result = $this->subscriptionService->completeUpgrade($payment, $plan);

        if ($result['success']) {
            return redirect()->route('student.subscription')
                ->with('success', $result['message']);
        }

        return redirect()->route('student.subscription')
            ->with('error', 'Failed to complete subscription upgrade.');
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return redirect()->route('student.subscription')
                ->with('error', 'No active subscription found.');
        }

        $this->subscriptionService->cancelSubscription($subscription);

        return redirect()->route('student.subscription')
            ->with('success', 'Subscription cancelled successfully.');
    }
}
