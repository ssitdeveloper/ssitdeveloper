<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $authUser): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function view(User $authUser, Payment $payment): bool
    {
        // Only admin can view any payment, or user can view their own
        return $authUser->role->value === 'admin' || $authUser->id === $payment->user_id;
    }

    public function create(User $authUser): bool
    {
        return false; // Payments created through payment gateway only
    }

    public function update(User $authUser, Payment $payment): bool
    {
        return $authUser->role->value === 'admin';
    }

    public function delete(User $authUser, Payment $payment): bool
    {
        return false; // Payments should not be deleted
    }
}
