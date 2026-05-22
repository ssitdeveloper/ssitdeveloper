<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true; // Students can see published questions
    }

    public function view(User $authUser, Question $question): bool
    {
        if ($authUser->role->value === 'admin') {
            return true;
        }

        // Students can only see published questions
        return $question->is_published;
    }

    public function create(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function update(User $authUser, Question $question): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function delete(User $authUser, Question $question): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function export(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function import(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }
}
