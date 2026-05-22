<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'user_id',
        'question_ids',
        'started_at',
        'submitted_at',
        'expires_at',
        'status',
        'time_remaining_seconds',
        'metadata',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Test relationship
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Test attempt answers relationship
     */
    public function answers(): HasMany
    {
        return $this->hasMany(TestAttemptAnswer::class);
    }

    /**
     * Test result relationship
     */
    public function result(): HasOne
    {
        return $this->hasOne(TestResult::class);
    }

    /**
     * Test bookmarks relationship
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(TestBookmark::class);
    }

    /**
     * Check if attempt is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if attempt is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if attempt has expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || now()->gt($this->expires_at);
    }

    /**
     * Check if attempt can be resumed
     */
    public function canResume(): bool
    {
        return $this->isInProgress() && !$this->isExpired();
    }

    /**
     * Get time remaining in seconds
     */
    public function getTimeRemainingSeconds(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        if ($this->time_remaining_seconds !== null) {
            return max(0, $this->time_remaining_seconds);
        }

        $remaining = now()->diffInSeconds($this->expires_at, false);
        return max(0, $remaining);
    }

    /**
     * Get formatted time remaining
     */
    public function getFormattedTimeRemaining(): string
    {
        $seconds = $this->getTimeRemainingSeconds();

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage(): float
    {
        $totalQuestions = count($this->question_ids);
        $answeredQuestions = $this->answers()->count();

        if ($totalQuestions === 0) {
            return 0;
        }

        return round(($answeredQuestions / $totalQuestions) * 100, 1);
    }

    /**
     * Get questions with answers
     */
    public function getQuestionsWithAnswers()
    {
        $questions = Question::with(['options', 'chapter.topic.subject'])
            ->whereIn('id', $this->question_ids)
            ->get()
            ->keyBy('id');

        // Reorder questions according to the stored order
        $orderedQuestions = collect();
        foreach ($this->question_ids as $questionId) {
            if (isset($questions[$questionId])) {
                $question = $questions[$questionId];
                $answer = $this->answers()->where('question_id', $questionId)->first();

                $question->user_answer = $answer;
                $orderedQuestions->push($question);
            }
        }

        return $orderedQuestions;
    }

    /**
     * Get current question index for navigation
     */
    public function getCurrentQuestionIndex(int $questionId): int
    {
        $index = array_search($questionId, $this->question_ids);
        return $index !== false ? $index + 1 : 1;
    }

    /**
     * Get next unanswered question
     */
    public function getNextUnansweredQuestion(): ?int
    {
        $answeredQuestionIds = $this->answers()->pluck('question_id')->toArray();

        foreach ($this->question_ids as $questionId) {
            if (!in_array($questionId, $answeredQuestionIds)) {
                return $questionId;
            }
        }

        return null;
    }

    /**
     * Mark attempt as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Mark attempt as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Update time remaining
     */
    public function updateTimeRemaining(int $seconds): void
    {
        $this->update(['time_remaining_seconds' => $seconds]);
    }

    /**
     * Scope for user's attempts
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for active attempts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed attempts
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for expired attempts
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }
}
