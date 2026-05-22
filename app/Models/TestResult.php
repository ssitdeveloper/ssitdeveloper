<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'total_questions',
        'attempted_questions',
        'correct_answers',
        'wrong_answers',
        'unanswered_questions',
        'total_marks',
        'obtained_marks',
        'negative_marks',
        'percentage',
        'rank',
        'percentile',
        'subject_wise_analysis',
        'difficulty_wise_analysis',
        'time_wise_analysis',
        'recommendations',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'percentile' => 'decimal:2',
        'subject_wise_analysis' => 'array',
        'difficulty_wise_analysis' => 'array',
        'time_wise_analysis' => 'array',
    ];

    /**
     * Test attempt relationship
     */
    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    /**
     * Get formatted percentage
     */
    public function getFormattedPercentage(): string
    {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Get formatted marks
     */
    public function getFormattedMarks(): string
    {
        return number_format($this->obtained_marks, 2) . '/' . number_format($this->total_marks, 2);
    }

    /**
     * Get accuracy percentage
     */
    public function getAccuracyPercentage(): float
    {
        if ($this->attempted_questions === 0) {
            return 0;
        }

        return round(($this->correct_answers / $this->attempted_questions) * 100, 2);
    }

    /**
     * Get rank prediction based on percentile
     */
    public function getPredictedRank(): ?int
    {
        if (!$this->percentile) {
            return null;
        }

        // This is a simplified rank prediction formula
        // In a real system, this would be based on historical data
        $totalCandidates = 150000; // Approximate NEET candidates
        $rank = round($totalCandidates * (1 - $this->percentile / 100));

        return max(1, $rank);
    }

    /**
     * Get performance summary
     */
    public function getPerformanceSummary(): array
    {
        return [
            'total_questions' => $this->total_questions,
            'attempted' => $this->attempted_questions,
            'correct' => $this->correct_answers,
            'wrong' => $this->wrong_answers,
            'unanswered' => $this->unanswered_questions,
            'accuracy' => $this->getAccuracyPercentage(),
            'marks_obtained' => $this->obtained_marks,
            'total_marks' => $this->total_marks,
            'percentage' => $this->percentage,
            'rank' => $this->rank,
            'predicted_rank' => $this->getPredictedRank(),
            'percentile' => $this->percentile,
        ];
    }

    /**
     * Get subject-wise performance
     */
    public function getSubjectWisePerformance(): array
    {
        if (!$this->subject_wise_analysis) {
            return [];
        }

        return $this->subject_wise_analysis;
    }

    /**
     * Get difficulty-wise performance
     */
    public function getDifficultyWisePerformance(): array
    {
        if (!$this->difficulty_wise_analysis) {
            return [];
        }

        return $this->difficulty_wise_analysis;
    }

    /**
     * Get recommendations
     */
    public function getRecommendations(): array
    {
        if (!$this->recommendations) {
            return [];
        }

        return is_array($this->recommendations) ? $this->recommendations : [$this->recommendations];
    }

    /**
     * Scope for high performers
     */
    public function scopeHighPerformers($query, $threshold = 80)
    {
        return $query->where('percentage', '>=', $threshold);
    }

    /**
     * Scope for low performers
     */
    public function scopeLowPerformers($query, $threshold = 40)
    {
        return $query->where('percentage', '<', $threshold);
    }
}