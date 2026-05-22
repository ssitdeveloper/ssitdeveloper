<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'explanation',
        'image_url',
        'order_by',
    ];

    protected $casts = [
        'question_id' => 'integer',
        'is_correct' => 'boolean',
        'order_by' => 'integer',
    ];

    // Relationships
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'selected_option_id');
    }

    // Helper methods
    public function isCorrect(): bool
    {
        return $this->is_correct;
    }

    public function getAnswerCount(): int
    {
        return $this->answers()->count();
    }

    public function getCorrectAnswerCount(): int
    {
        return $this->answers()->where('is_correct', true)->count();
    }
}
