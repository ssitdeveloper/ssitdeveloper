<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionPlan;
use App\Services\SubscriptionService;
use App\Services\PaymentService;
use App\Services\RazorpayService;
use App\Services\CouponService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function showPlans()
    {
        $plans = SubscriptionPlan::cases();
        $userSubscription = auth()->user()->activeSubscription();

        return view('student.subscriptions', [
            'plans' => $plans,
            'userSubscription' => $userSubscription,
        ]);
    }

    public function create(
        Request $request,
        RazorpayService $razorpayService,
        SubscriptionService $subscriptionService,
        CouponService $couponService
    ) {
        $validated = $request->validate([
            'plan' => 'required|in:monthly,quarterly,yearly,premium',
            'payment_method' => 'required|in:razorpay,stripe',
            'coupon_code' => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::from($validated['plan']);
        $amount = $plan->price();

        // Apply coupon if provided
        if ($request->coupon_code) {
            $coupon = $couponService->validateCoupon($request->coupon_code);
            if ($coupon) {
                $amount -= $couponService->calculateDiscount($coupon, $amount);
            }
        }

        if ($validated['payment_method'] === 'razorpay') {
            $order = $razorpayService->createOrder($amount, 'INR', [
                'plan' => $plan->value,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'key' => config('razorpay.key_id'),
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
            ]);
        }

        // Stripe handling would go here
    }

    public function verify(
        Request $request,
        RazorpayService $razorpayService,
        SubscriptionService $subscriptionService,
        PaymentService $paymentService
    ) {
        $validated = $request->validate([
            'payment_id' => 'required|string',
            'order_id' => 'required|string',
            'signature' => 'required|string',
            'plan' => 'required|in:monthly,quarterly,yearly,premium',
        ]);

        if (!$razorpayService->verifyPayment($validated['payment_id'], $validated['order_id'], $validated['signature'])) {
            return response()->json(['error' => 'Payment verification failed'], 400);
        }

        $plan = SubscriptionPlan::from($validated['plan']);
        $subscription = $subscriptionService->createSubscription(auth()->user(), $plan, $validated['payment_id']);

        $payment = $paymentService->createPayment(
            auth()->user(),
            $plan->price(),
            'razorpay',
            $subscription,
            $validated['payment_id']
        );

        $paymentService->completePayment($payment, $validated['payment_id']);

        return response()->json([
            'message' => 'Subscription activated',
            'subscription' => $subscription,
        ]);
    }

    public function paymentHistory()
    {
        $payments = auth()->user()->payments()->latest()->paginate(10);

        return view('student.payment-history', ['payments' => $payments]);
    }
}
