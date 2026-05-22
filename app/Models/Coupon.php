<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percentage',
        'discount_amount',
        'valid_from',
        'valid_until',
        'max_uses',
        'used_count',
        'applicable_plans',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'applicable_plans' => 'json',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('used_count', '<', 'max_uses')
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    // Methods
    public function isValid(): bool
    {
        return $this->is_active &&
               $this->used_count < $this->max_uses &&
               now()->isBetween($this->valid_from, $this->valid_until);
    }

    public function use()
    {
        $this->increment('used_count');
    }

    public function getDiscountAmount($price): float
    {
        if ($this->discount_percentage) {
            return ($price * $this->discount_percentage) / 100;
        }

        return $this->discount_amount;
    }
}
