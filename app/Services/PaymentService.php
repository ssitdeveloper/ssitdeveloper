<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;

class PaymentService
{
    public function createPayment(
        User $user,
        float $amount,
        string $paymentMethod,
        ?Subscription $subscription = null,
        ?string $transactionId = null
    ): Payment {
        return Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription?->id,
            'amount' => $amount,
            'currency' => 'INR',
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'status' => PaymentStatus::PENDING,
        ]);
    }

    public function completePayment(Payment $payment, string $transactionId = null): Payment
    {
        $payment->update([
            'status' => PaymentStatus::COMPLETED,
            'transaction_id' => $transactionId ?? $payment->transaction_id,
        ]);

        return $payment;
    }

    public function failPayment(Payment $payment): Payment
    {
        $payment->update([
            'status' => PaymentStatus::FAILED,
        ]);

        return $payment;
    }

    public function refundPayment(Payment $payment): Payment
    {
        $payment->update([
            'status' => PaymentStatus::REFUNDED,
        ]);

        return $payment;
    }

    public function getPaymentsByUser(User $user)
    {
        return $user->payments()->latest()->paginate(15);
    }

    public function getMonthlyRevenue()
    {
        return Payment::where('status', PaymentStatus::COMPLETED)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
    }
}
