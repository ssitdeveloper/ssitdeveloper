<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_questions_attempted',
        'total_tests_taken',
        'accuracy_percentage',
        'subject_wise_accuracy',
        'weak_subjects',
        'study_streak_days',
        'total_study_minutes',
    ];

    protected $casts = [
        'accuracy_percentage' => 'decimal:2',
        'subject_wise_accuracy' => 'json',
        'weak_subjects' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateFromTestAttempt(TestAttempt $attempt)
    {
        // Update basic stats
        $this->increment('total_tests_taken');
        $this->increment('total_study_minutes', $attempt->getTimeSpentMinutes());

        // Recalculate accuracy
        $this->recalculateAccuracy();
    }

    public function recalculateAccuracy()
    {
        $attempts = $this->user->testAttempts;

        if ($attempts->isEmpty()) {
            return;
        }

        $totalAnswers = $attempts->sum(fn($attempt) => $attempt->answers->count());
        $correctAnswers = $attempts->sum(fn($attempt) => $attempt->answers->where('is_correct', true)->count());

        $accuracy = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;

        $this->update([
            'accuracy_percentage' => $accuracy,
            'total_questions_attempted' => $totalAnswers,
        ]);
    }
}
