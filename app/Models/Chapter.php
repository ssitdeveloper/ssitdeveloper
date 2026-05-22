<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Znck\Eloquent\Relations\BelongsToThrough;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'name',
        'description',
        'order_by',
    ];

    protected $casts = [
        'topic_id' => 'integer',
        'order_by' => 'integer',
    ];

    // Relationships
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function subject(): BelongsToThrough
    {
        return $this->belongsToThrough(Subject::class, Topic::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function publishedQuestions(): HasMany
    {
        return $this->hasMany(Question::class)->where('is_published', true);
    }

    // Scopes
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_by');
    }

    // Helper methods
    public function getQuestionCount(): int
    {
        return $this->questions()->count();
    }

    public function getPublishedQuestionCount(): int
    {
        return $this->publishedQuestions()->count();
    }

    public function getSubjectName(): string
    {
        return $this->topic?->subject?->name ?? '';
    }

    public function getTopicName(): string
    {
        return $this->topic?->name ?? '';
    }
}
