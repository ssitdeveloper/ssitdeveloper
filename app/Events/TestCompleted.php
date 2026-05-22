<?php

namespace App\Events;

use App\Models\TestAttempt;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public TestAttempt $testAttempt,
    ) {}
}
