<?php

namespace App\Listeners;

use App\Events\SubscriptionExpiring;
use App\Mail\SubscriptionExpiringMail;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionExpiryNotification
{
    public function handle(SubscriptionExpiring $event): void
    {
        Mail::to($event->subscription->user->email)
            ->queue(new SubscriptionExpiringMail($event->subscription));
    }
}
