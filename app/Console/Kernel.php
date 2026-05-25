<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Check for subscriptions expiring in 3 days and attempt auto-renewal
        $schedule->job(new \App\Jobs\RenewExpiredSubscriptions())
            ->daily()
            ->at('00:00') // Run at midnight
            ->onOneServer()
            ->withoutOverlapping();

        // Check and mark expired subscriptions
        $schedule->call(function () {
            \App\Models\Subscription::where('status', \App\Enums\SubscriptionStatus::ACTIVE->value)
                ->whereDate('expires_at', '<=', now()->toDateString())
                ->update(['status' => \App\Enums\SubscriptionStatus::EXPIRED->value]);
        })->daily()->at('01:00');

        // Send subscription expiry reminders (7 days before expiry)
        $schedule->call(function () {
            $subscriptions = \App\Models\Subscription::where('status', \App\Enums\SubscriptionStatus::ACTIVE->value)
                ->whereDate('expires_at', '=', now()->addDays(7)->toDateString())
                ->get();

            foreach ($subscriptions as $subscription) {
                \Illuminate\Support\Facades\Mail::to($subscription->user->email)
                    ->send(new \App\Mail\SubscriptionExpiringMail($subscription));
            }
        })->daily()->at('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
