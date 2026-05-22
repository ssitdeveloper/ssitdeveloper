<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLearningPreference extends Model
{
    protected $fillable = [
        'user_id', 'show_explanation_after_answer', 'explanation_language',
        'questions_per_session', 'enable_adaptive_mode', 'show_hints',
        'hint_limit_per_question', 'shuffle_questions', 'shuffle_options',
        'show_difficulty_indicator', 'preferred_learning_style', 'notification_preferences'
    ];

    protected $casts = [
        'show_explanation_after_answer' => 'boolean',
        'enable_adaptive_mode' => 'boolean',
        'show_hints' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_difficulty_indicator' => 'boolean',
        'preferred_learning_style' => 'json',
        'notification_preferences' => 'json'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function updatePreferences(array $preferences): void
    {
        $this->update($preferences);
    }

    public function getPreferredLanguage(): string
    {
        return $this->explanation_language;
    }

    public function getLearningStyles(): array
    {
        return $this->preferred_learning_style ?? ['visual', 'auditory'];
    }

    public function shouldShowExplanation(): bool
    {
        return $this->show_explanation_after_answer;
    }

    public function isAdaptiveModeEnabled(): bool
    {
        return $this->enable_adaptive_mode;
    }

    public function getQuestionsPerSession(): int
    {
        return $this->questions_per_session;
    }

    public function getHintLimit(): int
    {
        return $this->hint_limit_per_question;
    }

    public static function getOrCreateForUser(User $user): self
    {
        return self::firstOrCreate(['user_id' => $user->id]);
    }
}
