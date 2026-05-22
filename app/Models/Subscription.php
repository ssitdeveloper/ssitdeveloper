<?php

namespace App\Models;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'started_at',
        'expires_at',
        'auto_renew',
        'payment_id',
    ];

    protected $casts = [
        'plan' => SubscriptionPlan::class,
        'status' => SubscriptionStatus::class,
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::ACTIVE)
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', SubscriptionStatus::EXPIRED)
            ->orWhere('expires_at', '<=', now());
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE &&
               $this->expires_at > now();
    }

    public function daysRemaining(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    public function renew()
    {
        $this->update([
            'status' => SubscriptionStatus::ACTIVE,
            'expires_at' => now()->addDays($this->plan->durationDays()),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => SubscriptionStatus::CANCELLED,
        ]);
    }
}
