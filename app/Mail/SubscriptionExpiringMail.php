<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(private Subscription $subscription) {}

    public function envelope(): Envelope
    {
        $daysLeft = $this->subscription->expires_at?->diffInDays(now()) ?? 0;
        return new Envelope(
            subject: "Your NEET LMS Subscription Expires in {$daysLeft} Days",
        );
    }

    public function content(): Content
    {
        $subscription = $this->subscription;
        $plan = $subscription->subscriptionPlan;
        $daysRemaining = $subscription->expires_at?->diffInDays(now()) ?? 0;

        // Calculate renewal details
        $renewalAmount = $plan ? ($plan->price / 100) : 0;

        return new Content(
            markdown: 'emails.subscription-expiring',
            with: [
                'studentName' => $subscription->user?->name,
                'subscription' => $subscription,
                'plan' => $plan,
                'daysRemaining' => max(0, $daysRemaining),
                'expiresAt' => $subscription->expires_at?->format('F d, Y'),
                'planName' => $plan?->name,
                'planBenefits' => $plan ? $this->getPlanBenefits($plan) : [],
                'renewalAmount' => number_format($renewalAmount, 2),
                'autoRenewalStatus' => $subscription->auto_renew ? 'Enabled' : 'Disabled',
                'renewUrl' => route('student.subscription.plans'),
                'manageUrl' => route('student.payment.history'),
            ],
        );
    }

    /**
     * Get plan benefits for display
     */
    private function getPlanBenefits($plan)
    {
        return [
            'Full access to ' . ($plan->name === 'PRO' ? '10,000+' : ($plan->name === 'PREMIUM' ? '5,000+' : '1,000+')) . ' questions',
            'Unlimited practice tests',
            'Detailed performance analytics',
            'Weekly performance insights',
            'Priority email support',
        ];
    }
}

