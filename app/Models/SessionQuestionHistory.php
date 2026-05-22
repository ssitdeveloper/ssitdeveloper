<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionQuestionHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'learning_session_id', 'question_id', 'question_order', 'visit_status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function learningSession(): BelongsTo
    {
        return $this->belongsTo(LearningSession::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Methods
    public function markAsVisited(): void
    {
        if ($this->visit_status === 'not_visited') {
            $this->visit_status = 'visited';
            $this->save();
        }
    }

    public function markAsAnswered(): void
    {
        $this->visit_status = 'answered';
        $this->save();
    }

    public function markAsReviewed(): void
    {
        $this->visit_status = 'reviewed';
        $this->save();
    }

    public function isVisited(): bool
    {
        return $this->visit_status !== 'not_visited';
    }

    public function isAnswered(): bool
    {
        return in_array($this->visit_status, ['answered', 'reviewed']);
    }
}
