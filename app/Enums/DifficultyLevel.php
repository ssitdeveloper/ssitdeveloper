<?php

namespace App\Enums;

enum DifficultyLevel: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function label(): string
    {
        return match($this) {
            self::EASY => 'Easy',
            self::MEDIUM => 'Medium',
            self::HARD => 'Hard',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::EASY => 'green',
            self::MEDIUM => 'yellow',
            self::HARD => 'red',
        };
    }
}
