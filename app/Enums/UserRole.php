<?php

namespace App\Enums;

enum UserRole: string
{
    case STUDENT = 'student';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case INSTRUCTOR = 'instructor';

    public function label(): string
    {
        return match($this) {
            self::STUDENT => 'Student',
            self::ADMIN => 'Administrator',
            self::MODERATOR => 'Moderator',
            self::INSTRUCTOR => 'Instructor',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::STUDENT => 'Can access questions, tests, and analytics',
            self::ADMIN => 'Full platform access and management',
            self::MODERATOR => 'Content moderation and question approval',
            self::INSTRUCTOR => 'Can create and manage content',
        };
    }
}
