<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(private Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation - Order #' . $this->payment->id,
        );
    }

    public function content(): Content
    {
        $subscription = $this->payment->subscription;
        $plan = $this->payment->subscription?->subscriptionPlan;

        $data = [
            'payment' => $this->payment,
            'subscription' => $subscription,
            'plan' => $plan,
            'studentName' => $this->payment->user?->name,
            'amount' => number_format($this->payment->amount / 100, 2),
            'currency' => config('payment.currency', 'USD'),
            'paymentDate' => $this->payment->created_at?->format('F d, Y'),
            'transactionId' => $this->payment->transaction_id,
            'subscriptionUrl' => $subscription ? route('student.payment.history') : null,
            'planExpiresAt' => $subscription?->expires_at?->format('F d, Y') ?? null,
        ];

        return new Content(
            markdown: 'emails.payment-confirmation',
            with: $data,
        );
    }
}

