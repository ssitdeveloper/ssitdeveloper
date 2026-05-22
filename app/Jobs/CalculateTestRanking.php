<?php

namespace App\Jobs;

use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateTestRanking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Test $test) {}

    public function handle(): void
    {
        $attempts = $this->test->attempts()
            ->where('status', 'completed')
            ->orderBy('percentage', 'desc')
            ->get();

        foreach ($attempts as $index => $attempt) {
            $attempt->update(['rank' => $index + 1]);
        }
    }
}
