<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_marked_for_review',
        'answered_at',
    ];

    protected $casts = [
        'is_marked_for_review' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Test attempt relationship
     */
    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    /**
     * Question relationship
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Selected option relationship
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'selected_option_id');
    }

    /**
     * Check if answer is correct
     */
    public function isCorrect(): bool
    {
        if (!$this->selected_option_id) {
            return false;
        }

        return $this->selectedOption && $this->selectedOption->is_correct;
    }

    /**
     * Get answer status for UI
     */
    public function getStatus(): string
    {
        if (!$this->selected_option_id) {
            return 'not_answered';
        }

        return $this->isCorrect() ? 'correct' : 'incorrect';
    }

    /**
     * Scope for marked for review
     */
    public function scopeMarkedForReview($query)
    {
        return $query->where('is_marked_for_review', true);
    }

    /**
     * Scope for answered questions
     */
    public function scopeAnswered($query)
    {
        return $query->whereNotNull('selected_option_id');
    }

    /**
     * Scope for unanswered questions
     */
    public function scopeUnanswered($query)
    {
        return $query->whereNull('selected_option_id');
    }
}