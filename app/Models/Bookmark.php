<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'question_id' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Scopes
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function getQuestionSubject(): string
    {
        return $this->question?->getSubjectName() ?? '';
    }

    public function getQuestionTopic(): string
    {
        return $this->question?->getTopicName() ?? '';
    }

    public function getQuestionChapter(): string
    {
        return $this->question?->getChapterName() ?? '';
    }
}
