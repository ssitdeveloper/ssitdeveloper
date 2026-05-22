<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminUser extends Model
{
    protected $fillable = [
        'user_id', 'role', 'permissions', 'is_active', 'last_login_at'
    ];

    protected $casts = [
        'permissions' => 'json',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}

class Banner extends Model
{
    protected $fillable = [
        'title', 'description', 'image_path', 'button_text', 'button_url',
        'placement', 'order', 'starts_at', 'ends_at', 'is_active'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && now()->isAfter($this->ends_at)) {
            return false;
        }

        return true;
    }

    public static function getActiveBanners(string $placement)
    {
        return self::where('placement', $placement)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('order')
            ->get();
    }
}

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

class NotificationsConfig extends Model
{
    protected $table = 'notifications_config';

    protected $fillable = [
        'key', 'title', 'template', 'type', 'variables', 'is_enabled'
    ];

    protected $casts = [
        'variables' => 'json',
        'is_enabled' => 'boolean'
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }
}

class UserNotification extends Model
{
    protected $fillable = [
        'user_id', 'notifications_config_id', 'title', 'message', 'data', 'is_read', 'read_at'
    ];

    protected $casts = [
        'data' => 'json',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function config(): BelongsTo
    {
        return $this->belongsTo(NotificationsConfig::class, 'notifications_config_id');
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}

class AnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'event_name', 'event_category', 'event_data',
        'user_agent', 'ip_address', 'created_at'
    ];

    protected $casts = [
        'event_data' => 'json',
        'created_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

class SystemSetting extends Model
{
    protected $fillable = [
        'key', 'value', 'type', 'description'
    ];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? self::castValue($setting->value, $setting->type) : $default;
    }

    public static function set(string $key, $value, string $type = 'string', string $description = ''): void
    {
        self::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
            'description' => $description
        ]);
    }

    private static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => $value === 'true' || $value === 1 || $value === true,
            'number' => (float)$value,
            'json' => json_decode($value, true),
            default => $value
        };
    }
}
