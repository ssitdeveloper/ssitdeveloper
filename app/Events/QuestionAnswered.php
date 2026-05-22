<?php

namespace App\Events;

use App\Models\Question;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionAnswered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Question $question,
        public int $userId,
        public bool $isCorrect,
    ) {}
}
