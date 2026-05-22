<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'receipt_url',
        'metadata',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'metadata' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    // Methods
    public function isCompleted(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    public function complete(string $transactionId = null)
    {
        $this->update([
            'status' => PaymentStatus::COMPLETED,
            'transaction_id' => $transactionId ?? $this->transaction_id,
        ]);
    }

    public function fail()
    {
        $this->update([
            'status' => PaymentStatus::FAILED,
        ]);
    }

    public function refund()
    {
        $this->update([
            'status' => PaymentStatus::REFUNDED,
        ]);
    }
}
