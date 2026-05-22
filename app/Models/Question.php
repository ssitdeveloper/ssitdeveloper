<?php

namespace App\Models;

use App\Enums\DifficultyLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'question_text',
        'difficulty_level',
        'type',
        'tags',
        'image_url',
        'explanation',
        'solution_video_url',
        'is_published',
        'views_count',
        'attempts_count',
    ];

    protected $casts = [
        'difficulty_level' => DifficultyLevel::class,
        'is_published' => 'boolean',
        'tags' => 'json',
        'views_count' => 'integer',
        'attempts_count' => 'integer',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function tests()
    {
        return $this->belongsToMany(Test::class, 'test_questions')->withPivot('order_by');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks');
    }

    public function topic(): BelongsToThrough
    {
        return $this->belongsToThrough(Topic::class, Chapter::class);
    }

    public function subject(): BelongsToThrough
    {
        return $this->belongsToThrough(Subject::class, [Topic::class, Chapter::class]);
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeDifficulty(Builder $query, DifficultyLevel $level): Builder
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeBySubject(Builder $query, int $subjectId): Builder
    {
        return $query->whereHas('chapter.topic', function ($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    public function scopeByTopic(Builder $query, int $topicId): Builder
    {
        return $query->whereHas('chapter', function ($q) use ($topicId) {
            $q->where('topic_id', $topicId);
        });
    }

    public function scopeByChapter(Builder $query, int $chapterId): Builder
    {
        return $query->where('chapter_id', $chapterId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('question_text', 'LIKE', "%{$search}%")
              ->orWhere('explanation', 'LIKE', "%{$search}%");
        });
    }

    // Methods
    public function getCorrectOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts_count');
    }

    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy()->where('user_id', $user->id)->exists();
    }

    public function getSubjectName(): string
    {
        return $this->chapter?->topic?->subject?->name ?? '';
    }

    public function getTopicName(): string
    {
        return $this->chapter?->topic?->name ?? '';
    }

    public function getChapterName(): string
    {
        return $this->chapter?->name ?? '';
    }

    public function getFormattedTags(): array
    {
        return $this->tags ?? [];
    }
}
