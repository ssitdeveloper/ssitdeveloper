<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningHintUsed extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'learning_session_id', 'user_id', 'question_id', 'hint_number', 'hint_text', 'time_to_hint_seconds'
    ];

    protected $casts = [
        'created_at' => 'datetime'
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
}
