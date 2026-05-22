<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
    case PREMIUM = 'premium';

    public function label(): string
    {
        return match($this) {
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly (3 Months)',
            self::YEARLY => 'Yearly',
            self::PREMIUM => 'Premium (Yearly)',
        };
    }

    public function price(): int
    {
        return match($this) {
            self::MONTHLY => 299,
            self::QUARTERLY => 799,
            self::YEARLY => 1999,
            self::PREMIUM => 4999,
        };
    }

    public function durationDays(): int
    {
        return match($this) {
            self::MONTHLY => 30,
            self::QUARTERLY => 90,
            self::YEARLY => 365,
            self::PREMIUM => 365,
        };
    }

    public function features(): array
    {
        return match($this) {
            self::MONTHLY => [
                'question_bank' => true,
                'learning_mode' => true,
                'mock_tests' => false,
                'analytics' => false,
                'offline_download' => false,
                'priority_support' => false,
            ],
            self::QUARTERLY => [
                'question_bank' => true,
                'learning_mode' => true,
                'mock_tests' => true,
                'analytics' => true,
                'offline_download' => false,
                'priority_support' => false,
            ],
            self::YEARLY => [
                'question_bank' => true,
                'learning_mode' => true,
                'mock_tests' => true,
                'analytics' => true,
                'offline_download' => true,
                'priority_support' => false,
            ],
            self::PREMIUM => [
                'question_bank' => true,
                'learning_mode' => true,
                'mock_tests' => true,
                'analytics' => true,
                'offline_download' => true,
                'priority_support' => true,
            ],
        };
    }
}
