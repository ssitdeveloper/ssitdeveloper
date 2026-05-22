<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function view(User $authUser, User $user): bool
    {
        return $authUser->role->value === 'admin' || $authUser->id === $user->id;
    }

    public function create(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function update(User $authUser, User $user): bool
    {
        if ($authUser->role->value === 'admin') {
            return true;
        }

        // Users can only update their own profile, but cannot change their role
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user): bool
    {
        return $authUser->role->value === 'admin' && $authUser->id !== $user->id;
    }

    public function restore(User $authUser, User $user): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function forceDelete(User $authUser, User $user): bool
    {
        return $authUser->role->value === 'admin' && $authUser->id !== $user->id;
    }
}
