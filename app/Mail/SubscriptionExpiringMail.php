<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private Subscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your NEET LMS subscription is expiring soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expiring',
            with: [
                'subscription' => $this->subscription,
                'daysRemaining' => $this->subscription->daysRemaining(),
            ],
        );
    }
}
