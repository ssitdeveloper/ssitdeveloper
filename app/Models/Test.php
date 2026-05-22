<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'duration_minutes',
        'total_questions',
        'subject_distribution',
        'difficulty_distribution',
        'negative_marking',
        'marks_per_question',
        'is_active',
        'scheduled_at',
        'expires_at',
        'instructions',
    ];

    protected $casts = [
        'subject_distribution' => 'array',
        'difficulty_distribution' => 'array',
        'instructions' => 'array',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'negative_marking' => 'decimal:2',
        'marks_per_question' => 'decimal:2',
    ];

    /**
     * Generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($test) {
            if (empty($test->slug)) {
                $test->slug = Str::slug($test->title);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope: Get only published/active tests
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Test attempts relationship
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Active attempts relationship
     */
    public function activeAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class)->where('status', 'in_progress');
    }

    /**
     * Completed attempts relationship
     */
    public function completedAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class)->where('status', 'completed');
    }

    /**
     * Test analytics relationship
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(TestAnalytics::class);
    }

    /**
     * Questions relationship
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_questions')
            ->withPivot('order_by')
            ->orderBy('test_questions.order_by');
    }

    /**
     * Get questions randomized for a test attempt
     */
    public function getQuestionsRandomized()
    {
        return $this->questions()
            ->inRandomOrder()
            ->get();
    }

    /**
     * Check if test is available for attempt
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->scheduled_at && $now->lt($this->scheduled_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if test is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_at && now()->lt($this->scheduled_at);
    }

    /**
     * Check if test has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Get total marks for the test
     */
    public function getTotalMarks(): float
    {
        return $this->total_questions * $this->marks_per_question;
    }

    /**
     * Get duration in seconds
     */
    public function getDurationSeconds(): int
    {
        return $this->duration_minutes * 60;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }

        return sprintf('%d minutes', $minutes);
    }

    /**
     * Get subject distribution as formatted string
     */
    public function getSubjectDistributionFormatted(): string
    {
        if (!$this->subject_distribution) {
            return 'Not specified';
        }

        $parts = [];
        foreach ($this->subject_distribution as $subject => $count) {
            $parts[] = ucfirst($subject) . ': ' . $count;
        }

        return implode(', ', $parts);
    }

    /**
     * Get difficulty distribution as formatted string
     */
    public function getDifficultyDistributionFormatted(): string
    {
        if (!$this->difficulty_distribution) {
            return 'Not specified';
        }

        $parts = [];
        foreach ($this->difficulty_distribution as $difficulty => $percentage) {
            $parts[] = ucfirst($difficulty) . ': ' . $percentage . '%';
        }

        return implode(', ', $parts);
    }

    /**
     * Scope for active tests
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for available tests
     */
    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for scheduled tests
     */
    public function scopeScheduled($query)
    {
        return $query->active()
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }
}
