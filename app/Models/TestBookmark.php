<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestBookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_attempt_id',
        'question_id',
        'notes',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Test attempt relationship
     */
    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    /**
     * Question relationship
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Scope for user's bookmarks
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for test attempt
     */
    public function scopeForTestAttempt($query, $attemptId)
    {
        return $query->where('test_attempt_id', $attemptId);
    }
}