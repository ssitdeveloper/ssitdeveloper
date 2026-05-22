<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'order_by',
    ];

    protected $casts = [
        'order_by' => 'integer',
    ];

    // Relationships
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('order_by');
    }

    public function chapters(): HasManyThrough
    {
        return $this->hasManyThrough(Chapter::class, Topic::class);
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Question::class, Topic::class);
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

    public function getTopicCount(): int
    {
        return $this->topics()->count();
    }
}
