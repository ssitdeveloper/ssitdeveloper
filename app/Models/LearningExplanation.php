<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningExplanation extends Model
{
    protected $fillable = [
        'question_id', 'detailed_explanation', 'key_concepts', 'similar_questions',
        'views_count', 'helpful_count', 'unhelpful_count', 'type',
        'video_url', 'image_path', 'difficulty_level'
    ];

    protected $casts = [
        'similar_questions' => 'json'
    ];

    // Relationships
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function markAsUnhelpful(): void
    {
        $this->increment('unhelpful_count');
    }

    public function getHelpfulPercentage(): float
    {
        $total = $this->helpful_count + $this->unhelpful_count;
        return $total > 0 ? round(($this->helpful_count / $total) * 100, 2) : 0;
    }

    public function getSimilarQuestions()
    {
        if (!$this->similar_questions) {
            return collect();
        }

        return Question::whereIn('id', $this->similar_questions)->get();
    }

    public function getKeyConceptsList(): array
    {
        if (!$this->key_concepts) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $this->key_concepts)));
    }
}
