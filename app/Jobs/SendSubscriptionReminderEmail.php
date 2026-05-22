<?php

namespace App\Jobs;

use App\Mail\SubscriptionExpiringMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Subscription $subscription) {}

    public function handle(): void
    {
        Mail::to($this->subscription->user->email)
            ->send(new SubscriptionExpiringMail($this->subscription));
    }
}
