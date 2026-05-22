<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWeakTopic extends Model
{
    protected $fillable = [
        'user_id', 'subject_id', 'topic_id', 'total_attempts', 'correct_attempts',
        'accuracy_percentage', 'difficulty_score', 'status', 'last_attempted_at',
        'last_improved_at', 'trend_data', 'recommendations'
    ];

    protected $casts = [
        'last_attempted_at' => 'datetime',
        'last_improved_at' => 'datetime',
        'trend_data' => 'json',
        'recommendations' => 'json'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    // Methods
    public function updateAccuracy(): void
    {
        $accuracy = $this->total_attempts > 0
            ? round(($this->correct_attempts / $this->total_attempts) * 100, 2)
            : 0;

        $this->accuracy_percentage = $accuracy;
        $this->determineDifficulty($accuracy);
        $this->save();
    }

    public function addAttempt(bool $isCorrect): void
    {
        $this->total_attempts++;
        if ($isCorrect) {
            $this->correct_attempts++;
            $this->last_improved_at = now();
        }
        $this->last_attempted_at = now();

        $this->updateAccuracy();
    }

    private function determineDifficulty(float $accuracy): void
    {
        if ($accuracy < 40) {
            $this->status = 'weak';
            $this->difficulty_score = 80;
        } elseif ($accuracy < 60) {
            $this->status = 'weak';
            $this->difficulty_score = 60;
        } elseif ($accuracy < 80) {
            $this->status = 'average';
            $this->difficulty_score = 40;
        } else {
            $this->status = 'good';
            $this->difficulty_score = 20;
        }

        if ($accuracy >= 90) {
            $this->status = 'excellent';
            $this->difficulty_score = 0;
        }
    }

    public function isWeak(): bool
    {
        return $this->status === 'weak';
    }

    public function needsImprovement(): bool
    {
        return in_array($this->status, ['weak', 'average']);
    }

    public function getTrendStatus(): string
    {
        if (!$this->trend_data) {
            return 'stable';
        }

        $trend = $this->trend_data;
        if (count($trend) < 2) {
            return 'stable';
        }

        $recent = array_slice($trend, -3);
        $avgRecent = array_sum($recent) / count($recent);
        $avgBefore = array_sum(array_slice($trend, 0, -3)) / max(1, count($trend) - 3);

        if ($avgRecent > $avgBefore + 5) {
            return 'improving';
        } elseif ($avgRecent < $avgBefore - 5) {
            return 'declining';
        }

        return 'stable';
    }

    public function recordTrend(float $accuracy): void
    {
        $trend = $this->trend_data ?? [];
        $trend[] = $accuracy;

        // Keep only last 30 attempts
        if (count($trend) > 30) {
            $trend = array_slice($trend, -30);
        }

        $this->trend_data = $trend;
        $this->save();
    }

    public function getRecommendations(): array
    {
        return $this->recommendations ?? [];
    }

    public function setRecommendations(array $recommendations): void
    {
        $this->recommendations = $recommendations;
        $this->save();
    }
}
