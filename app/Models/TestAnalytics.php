<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'score_range_min',
        'score_range_max',
        'student_count',
        'percentile',
    ];

    protected $casts = [
        'score_range_min' => 'decimal:2',
        'score_range_max' => 'decimal:2',
        'percentile' => 'decimal:2',
    ];

    /**
     * Test relationship
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Get percentile for a score
     */
    public static function getPercentileForScore(int $testId, float $score): ?float
    {
        $analytics = self::where('test_id', $testId)
            ->where('score_range_min', '<=', $score)
            ->where('score_range_max', '>=', $score)
            ->first();

        return $analytics ? $analytics->percentile : null;
    }

    /**
     * Get rank for a score
     */
    public static function getRankForScore(int $testId, float $score): ?int
    {
        $analytics = self::where('test_id', $testId)
            ->where('score_range_min', '<=', $score)
            ->orderBy('score_range_max', 'desc')
            ->first();

        if (!$analytics) {
            return null;
        }

        // Calculate rank based on percentile
        $totalStudents = self::where('test_id', $testId)->sum('student_count');
        $rank = round($totalStudents * (1 - $analytics->percentile / 100));

        return max(1, $rank);
    }

    /**
     * Update analytics for a test
     */
    public static function updateAnalyticsForTest(int $testId): void
    {
        // Get all completed attempts for the test
        $results = TestResult::whereHas('testAttempt', function ($query) use ($testId) {
            $query->where('test_id', $testId);
        })->get();

        if ($results->isEmpty()) {
            return;
        }

        // Clear existing analytics
        self::where('test_id', $testId)->delete();

        // Group results by score ranges
        $scoreRanges = [];
        $minScore = $results->min('obtained_marks');
        $maxScore = $results->max('obtained_marks');
        $rangeSize = max(1, ($maxScore - $minScore) / 20); // 20 ranges

        for ($i = 0; $i < 20; $i++) {
            $rangeMin = $minScore + ($i * $rangeSize);
            $rangeMax = $minScore + (($i + 1) * $rangeSize);

            $count = $results->whereBetween('obtained_marks', [$rangeMin, $rangeMax])->count();

            if ($count > 0) {
                $scoreRanges[] = [
                    'min' => $rangeMin,
                    'max' => $rangeMax,
                    'count' => $count,
                ];
            }
        }

        // Calculate percentiles
        $totalStudents = $results->count();
        $cumulativeCount = 0;

        foreach ($scoreRanges as $range) {
            $cumulativeCount += $range['count'];
            $percentile = round(($cumulativeCount / $totalStudents) * 100, 2);

            self::create([
                'test_id' => $testId,
                'score_range_min' => $range['min'],
                'score_range_max' => $range['max'],
                'student_count' => $range['count'],
                'percentile' => $percentile,
            ]);
        }
    }

    /**
     * Get score distribution
     */
    public static function getScoreDistribution(int $testId): array
    {
        return self::where('test_id', $testId)
            ->orderBy('score_range_min')
            ->get()
            ->map(function ($analytic) {
                return [
                    'range' => number_format($analytic->score_range_min, 1) . ' - ' . number_format($analytic->score_range_max, 1),
                    'count' => $analytic->student_count,
                    'percentile' => $analytic->percentile,
                ];
            })
            ->toArray();
    }
}