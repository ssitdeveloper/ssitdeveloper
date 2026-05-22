<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningProgress extends Model
{
    protected $fillable = [
        'user_id', 'subject_id', 'total_questions_attempted', 'total_correct',
        'overall_accuracy', 'total_time_minutes', 'streak_days', 'last_activity_at',
        'weekly_stats', 'monthly_stats', 'chapter_progress'
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'weekly_stats' => 'json',
        'monthly_stats' => 'json',
        'chapter_progress' => 'json'
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

    // Methods
    public function updateFromSession(LearningSession $session): void
    {
        $this->total_questions_attempted += $session->questions_completed;
        $this->total_correct += $session->correct_answers;
        $this->total_time_minutes += intdiv($session->getElapsedSeconds(), 60);
        $this->last_activity_at = now();

        $this->updateAccuracy();
        $this->updateStreak();
        $this->updateStats($session);

        $this->save();
    }

    private function updateAccuracy(): void
    {
        if ($this->total_questions_attempted > 0) {
            $this->overall_accuracy = round(
                ($this->total_correct / $this->total_questions_attempted) * 100,
                2
            );
        }
    }

    private function updateStreak(): void
    {
        $lastActivity = $this->last_activity_at?->startOfDay();
        $today = now()->startOfDay();

        if (!$lastActivity) {
            $this->streak_days = 1;
            return;
        }

        $daysDiff = $today->diffInDays($lastActivity);

        if ($daysDiff === 1) {
            $this->streak_days++;
        } elseif ($daysDiff > 1) {
            $this->streak_days = 1;
        }
    }

    private function updateStats(LearningSession $session): void
    {
        $weeklyStats = $this->weekly_stats ?? [];
        $weekKey = now()->format('W-Y');

        if (!isset($weeklyStats[$weekKey])) {
            $weeklyStats[$weekKey] = [
                'questions' => 0,
                'correct' => 0,
                'time_minutes' => 0
            ];
        }

        $weeklyStats[$weekKey]['questions'] += $session->questions_completed;
        $weeklyStats[$weekKey]['correct'] += $session->correct_answers;
        $weeklyStats[$weekKey]['time_minutes'] += intdiv($session->getElapsedSeconds(), 60);

        // Keep only last 12 weeks
        if (count($weeklyStats) > 12) {
            array_shift($weeklyStats);
        }

        $this->weekly_stats = $weeklyStats;

        // Update monthly stats
        $monthlyStats = $this->monthly_stats ?? [];
        $monthKey = now()->format('m-Y');

        if (!isset($monthlyStats[$monthKey])) {
            $monthlyStats[$monthKey] = [
                'questions' => 0,
                'correct' => 0,
                'time_minutes' => 0
            ];
        }

        $monthlyStats[$monthKey]['questions'] += $session->questions_completed;
        $monthlyStats[$monthKey]['correct'] += $session->correct_answers;
        $monthlyStats[$monthKey]['time_minutes'] += intdiv($session->getElapsedSeconds(), 60);

        if (count($monthlyStats) > 12) {
            array_shift($monthlyStats);
        }

        $this->monthly_stats = $monthlyStats;
    }

    public function getWeeklyStats(): array
    {
        return $this->weekly_stats ?? [];
    }

    public function getMonthlyStats(): array
    {
        return $this->monthly_stats ?? [];
    }

    public function getChapterProgress(): array
    {
        return $this->chapter_progress ?? [];
    }

    public function updateChapterProgress(int $chapterId, float $accuracy): void
    {
        $progress = $this->chapter_progress ?? [];
        $progress[$chapterId] = $accuracy;
        $this->chapter_progress = $progress;
        $this->save();
    }

    public static function getOrCreateForSubject(User $user, Subject $subject): self
    {
        return self::firstOrCreate([
            'user_id' => $user->id,
            'subject_id' => $subject->id
        ]);
    }

    public function getTotalHours(): float
    {
        return round($this->total_time_minutes / 60, 2);
    }

    public function getAverageTimePerQuestion(): int
    {
        if ($this->total_questions_attempted === 0) {
            return 0;
        }

        return intdiv($this->total_time_minutes * 60, $this->total_questions_attempted);
    }
}
