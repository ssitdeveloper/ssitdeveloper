<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
