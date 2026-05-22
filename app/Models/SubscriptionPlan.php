<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'currency', 'duration_days',
        'features', 'max_tests', 'max_practice_questions', 'has_analytics',
        'has_adaptive_learning', 'has_doubt_clearing', 'order', 'is_active'
    ];

    protected $casts = [
        'features' => 'json',
        'has_analytics' => 'boolean',
        'has_adaptive_learning' => 'boolean',
        'has_doubt_clearing' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getFeatures(): array
    {
        return $this->features ?? [];
    }
}
