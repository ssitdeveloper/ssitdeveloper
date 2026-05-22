<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id', 'subscription_plan_id', 'amount', 'currency',
        'payment_gateway', 'gateway_transaction_id', 'status',
        'gateway_response', 'completed_at', 'refunded_at'
    ];

    protected $casts = [
        'gateway_response' => 'json',
        'completed_at' => 'datetime',
        'refunded_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function markAsCompleted(array $response = []): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'gateway_response' => $response
        ]);
    }

    public function refund(): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now()
        ]);
    }
}
