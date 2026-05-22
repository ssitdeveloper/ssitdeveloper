<?php

namespace App\Policies;

use App\Models\TestAttempt;
use App\Models\User;

class TestAttemptPolicy
{
    public function viewAny(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function view(User $authUser, TestAttempt $attempt): bool
    {
        return $authUser->role->value === 'admin' || $authUser->id === $attempt->user_id;
    }

    public function create(User $authUser): bool
    {
        return $authUser->role->value === 'student';
    }

    public function update(User $authUser, TestAttempt $attempt): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function delete(User $authUser, TestAttempt $attempt): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function recordAnswer(User $authUser, TestAttempt $attempt): bool
    {
        return $authUser->id === $attempt->user_id && $attempt->status === 'in_progress';
    }
}
