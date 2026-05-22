<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
        'description',
        'order_by',
    ];

    protected $casts = [
        'subject_id' => 'integer',
        'order_by' => 'integer',
    ];

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order_by');
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Question::class, Chapter::class);
    }

    public function publishedQuestions(): HasManyThrough
    {
        return $this->questions()->where('questions.is_published', true);
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

    public function getChapterCount(): int
    {
        return $this->chapters()->count();
    }
}
