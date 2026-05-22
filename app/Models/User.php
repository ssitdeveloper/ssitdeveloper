<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'role',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => UserRole::class,
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->latest('expires_at')
            ->first();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function answers()
    {
        return $this->hasManyThrough(Answer::class, TestAttempt::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Question::class, 'bookmarks');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function analytics()
    {
        return $this->hasOne(Analytics::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStudents($query)
    {
        return $query->where('role', UserRole::STUDENT);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::ADMIN);
    }

    // Methods
    public function isStudent(): bool
    {
        return $this->role === UserRole::STUDENT;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === UserRole::MODERATOR;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function canAccessFeature(string $feature): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $subscription = $this->activeSubscription();
        if (!$subscription) {
            return false;
        }

        return $subscription->plan->features()[$feature] ?? false;
    }
}
