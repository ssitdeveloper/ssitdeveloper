<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningQuestionAttempt extends Model
{
    protected $fillable = [
        'learning_session_id', 'user_id', 'question_id', 'selected_option_id',
        'is_correct', 'time_spent_seconds', 'attempt_number', 'user_explanation',
        'is_bookmarked', 'is_reviewed', 'difficulty_rating', 'metadata'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'is_bookmarked' => 'boolean',
        'is_reviewed' => 'boolean',
        'metadata' => 'json'
    ];

    // Relationships
    public function learningSession(): BelongsTo
    {
        return $this->belongsTo(LearningSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'selected_option_id');
    }

    // Methods
    public function getCorrectOption()
    {
        return $this->question->options()->where('is_correct', true)->first();
    }

    public function getExplanation()
    {
        return $this->question->explanation;
    }

    public function markAsReviewed(): void
    {
        $this->update(['is_reviewed' => true]);
    }

    public function toggleBookmark(): void
    {
        $this->update(['is_bookmarked' => !$this->is_bookmarked]);
    }

    public function ratesDifficulty(string $rating): void
    {
        $this->update(['difficulty_rating' => $rating]);
    }

    public function getStatus(): string
    {
        if (!$this->selected_option_id) {
            return 'unanswered';
        }

        return $this->is_correct ? 'correct' : 'incorrect';
    }

    public function getAnswer(): ?string
    {
        return $this->selectedOption?->option_text ?? null;
    }

    public function getCorrectAnswer(): ?string
    {
        return $this->getCorrectOption()?->option_text ?? null;
    }

    public function recordHintUsed(string $hint): void
    {
        if (!isset($this->metadata['hints_used'])) {
            $this->metadata = array_merge($this->metadata ?? [], ['hints_used' => []]);
        }

        $this->metadata['hints_used'][] = [
            'hint' => $hint,
            'used_at' => now()->toDateTimeString()
        ];

        $this->save();
    }

    public function getHintsUsed(): array
    {
        return $this->metadata['hints_used'] ?? [];
    }

    public function getTimeSpentFormatted(): string
    {
        $seconds = $this->time_spent_seconds;
        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;

        return "{$minutes}m {$secs}s";
    }
}
