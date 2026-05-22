<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class LearningSession extends Model
{
    protected $fillable = [
        'user_id', 'subject_id', 'topic_id', 'chapter_id', 'mode',
        'total_questions', 'questions_completed', 'correct_answers',
        'current_question_index', 'session_data', 'status',
        'started_at', 'paused_at', 'completed_at'
    ];

    protected $casts = [
        'session_data' => 'json',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
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

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(LearningQuestionAttempt::class);
    }

    public function questionHistory(): HasMany
    {
        return $this->hasMany(SessionQuestionHistory::class);
    }

    public function hintsUsed(): HasMany
    {
        return $this->hasMany(LearningHintUsed::class);
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getProgressPercentage(): float
    {
        return $this->total_questions > 0
            ? round(($this->questions_completed / $this->total_questions) * 100, 2)
            : 0;
    }

    public function getAccuracyPercentage(): float
    {
        return $this->questions_completed > 0
            ? round(($this->correct_answers / $this->questions_completed) * 100, 2)
            : 0;
    }

    public function getElapsedSeconds(): int
    {
        $start = $this->started_at;
        $end = $this->paused_at ?? $this->completed_at ?? now();
        return $start ? $start->diffInSeconds($end) : 0;
    }

    public function getCurrentQuestion()
    {
        return $this->attempts()
            ->where('attempt_number', 1)
            ->orderBy('created_at', 'desc')
            ->first()?->question;
    }

    public function canResume(): bool
    {
        return $this->status === 'paused' &&
               $this->questions_completed < $this->total_questions;
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'active',
            'paused_at' => null
        ]);
    }

    public function pause(): void
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now()
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function getNextQuestion()
    {
        $unvisited = $this->questionHistory()
            ->where('visit_status', 'not_visited')
            ->first();

        return $unvisited?->question;
    }

    public function getSummary(): array
    {
        return [
            'total_questions' => $this->total_questions,
            'completed' => $this->questions_completed,
            'correct' => $this->correct_answers,
            'wrong' => $this->questions_completed - $this->correct_answers,
            'accuracy' => $this->getAccuracyPercentage(),
            'progress' => $this->getProgressPercentage(),
            'elapsed_seconds' => $this->getElapsedSeconds(),
        ];
    }
}
