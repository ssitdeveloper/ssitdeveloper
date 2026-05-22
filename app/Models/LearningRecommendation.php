<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningRecommendation extends Model
{
    protected $fillable = [
        'user_id', 'subject_id', 'topic_id', 'recommendation_type',
        'recommendation_text', 'priority', 'estimated_time_minutes',
        'target_question_ids', 'is_active', 'is_accepted',
        'accepted_at', 'completed_at'
    ];

    protected $casts = [
        'target_question_ids' => 'json',
        'is_active' => 'boolean',
        'is_accepted' => 'boolean',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime'
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
    public function accept(): void
    {
        $this->update([
            'is_accepted' => true,
            'accepted_at' => now()
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'is_accepted' => false,
            'is_active' => false
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'completed_at' => now(),
            'is_active' => false
        ]);
    }

    public function getTargetQuestions()
    {
        if (!$this->target_question_ids) {
            return collect();
        }

        return Question::whereIn('id', $this->target_question_ids)->get();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isPending(): bool
    {
        return $this->is_accepted === null && $this->is_active;
    }

    public function isRejected(): bool
    {
        return $this->is_accepted === false;
    }

    public static function getPriorityLabel(int $priority): string
    {
        return match (true) {
            $priority >= 80 => 'Critical',
            $priority >= 60 => 'High',
            $priority >= 40 => 'Medium',
            default => 'Low'
        };
    }
}
