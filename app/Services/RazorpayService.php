<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('razorpay.key_id'),
            config('razorpay.key_secret')
        );
    }

    public function createOrder(float $amount, string $currency = 'INR', array $meta = []): array
    {
        $order = $this->api->order->create([
            'amount' => (int) ($amount * 100), // Convert to paise
            'currency' => $currency,
            'receipt' => uniqid(),
            'notes' => $meta,
        ]);

        return $order->toArray();
    }

    public function verifyPayment(string $paymentId, string $orderId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'signature' => $signature,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPayment(string $paymentId): array
    {
        $payment = $this->api->payment->fetch($paymentId);

        return $payment->toArray();
    }

    public function createRefund(string $paymentId, float $amount = null): array
    {
        $refund = $this->api->payment->fetch($paymentId)->refund([
            'amount' => $amount ? (int) ($amount * 100) : null,
        ]);

        return $refund->toArray();
    }
}
