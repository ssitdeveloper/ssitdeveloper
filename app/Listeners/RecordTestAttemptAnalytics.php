<?php

namespace App\Listeners;

use App\Events\TestCompleted;
use App\Services\AnalyticsService;

class RecordTestAttemptAnalytics
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function handle(TestCompleted $event): void
    {
        $this->analyticsService->updateFromTestAttempt($event->testAttempt);
    }
}
