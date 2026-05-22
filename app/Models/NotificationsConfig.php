<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
