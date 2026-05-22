<?php

namespace App\Services;

use App\Models\UserWeakTopic;
use App\Models\Topic;
use App\Models\Subject;
use App\Models\User;
use App\Models\LearningSession;
use Illuminate\Support\Facades\DB;

class WeakTopicDetectionService
{
    private const CRITICAL_THRESHOLD = 40;  // < 40% accuracy
    private const HIGH_THRESHOLD = 55;      // < 55% accuracy
    private const MEDIUM_THRESHOLD = 70;    // < 70% accuracy

    /**
     * Detect weak topics for a user
     */
    public function detectWeakTopics(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Get all topics the user has attempted
        $topicPerformance = $this->calculateTopicPerformance($userId);

        foreach ($topicPerformance as $topic) {
            $severity = $this->determineSeverity($topic['accuracy']);

            if ($severity !== 'strong') {
                UserWeakTopic::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'topic_id' => $topic['topic_id'],
                    ],
                    [
                        'subject_id' => $topic['subject_id'],
                        'weak_score' => round(100 - $topic['accuracy'], 2),
                        'total_attempts' => $topic['attempts'],
                        'correct_attempts' => $topic['correct'],
                        'accuracy' => $topic['accuracy'],
                        'severity' => $severity,
                        'recommendation_status' => 'pending',
                        'last_attempt_at' => $topic['last_attempt'],
                    ]
                );
            } else {
                // Remove from weak topics if user has improved
                UserWeakTopic::where('user_id', $userId)
                    ->where('topic_id', $topic['topic_id'])
                    ->delete();
            }
        }
    }

    /**
     * Calculate accuracy for each topic
     */
    private function calculateTopicPerformance(int $userId): array
    {
        $results = DB::select("
            SELECT
                t.id as topic_id,
                s.id as subject_id,
                s.name as subject_name,
                t.name as topic_name,
                COUNT(ua.id) as attempts,
                SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) as correct,
                ROUND(100.0 * SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) / COUNT(ua.id), 2) as accuracy,
                MAX(ua.updated_at) as last_attempt
            FROM users u
            JOIN learning_question_attempts ua ON u.id = ua.user_id
            JOIN questions q ON ua.question_id = q.id
            JOIN topics t ON q.topic_id = t.id
            JOIN subjects s ON t.subject_id = s.id
            WHERE u.id = ?
            GROUP BY t.id, s.id
        ", [$userId]);

        return collect($results)->map(fn($item) => (array)$item)->toArray();
    }

    /**
     * Determine severity based on accuracy
     */
    private function determineSeverity(float $accuracy): string
    {
        if ($accuracy < self::CRITICAL_THRESHOLD) {
            return 'critical';
        } elseif ($accuracy < self::HIGH_THRESHOLD) {
            return 'high';
        } elseif ($accuracy < self::MEDIUM_THRESHOLD) {
            return 'medium';
        }

        return 'strong';
    }

    /**
     * Get weak topics for a user
     */
    public function getWeakTopics(int $userId, ?string $severity = null): array
    {
        $query = UserWeakTopic::where('user_id', $userId)
            ->with(['topic.subject'])
            ->orderByRaw("CASE
                WHEN severity = 'critical' THEN 1
                WHEN severity = 'high' THEN 2
                WHEN severity = 'medium' THEN 3
                ELSE 4
            END");

        if ($severity && $severity !== 'all') {
            $query->where('severity', $severity);
        }

        return $query->get()->map(function ($topic) {
            return [
                'id' => $topic->id,
                'subject' => $topic->topic->subject->name,
                'topic' => $topic->topic->name,
                'weak_score' => $topic->weak_score,
                'total_attempts' => $topic->total_attempts,
                'correct_attempts' => $topic->correct_attempts,
                'accuracy' => $topic->accuracy,
                'severity' => $topic->severity,
                'last_attempt_at' => $topic->last_attempt_at,
                'detected_at' => $topic->detected_at,
            ];
        })->toArray();
    }

    /**
     * Get weak topic summary
     */
    public function getWeakTopicSummary(int $userId): array
    {
        $weakTopics = UserWeakTopic::where('user_id', $userId)->get();

        return [
            'total_weak_topics' => $weakTopics->count(),
            'critical_count' => $weakTopics->where('severity', 'critical')->count(),
            'high_count' => $weakTopics->where('severity', 'high')->count(),
            'medium_count' => $weakTopics->where('severity', 'medium')->count(),
            'severity_distribution' => [
                'critical' => $weakTopics->where('severity', 'critical')->count(),
                'high' => $weakTopics->where('severity', 'high')->count(),
                'medium' => $weakTopics->where('severity', 'medium')->count(),
            ]
        ];
    }

    /**
     * Mark weak topic as acknowledged
     */
    public function acknowledgeWeakTopic(int $weakTopicId): void
    {
        UserWeakTopic::findOrFail($weakTopicId)->update([
            'recommendation_status' => 'acknowledged',
        ]);
    }

    /**
     * Get recommendations for weak topics
     */
    public function getWeakTopicRecommendations(int $userId): array
    {
        $weakTopics = UserWeakTopic::where('user_id', $userId)
            ->where('recommendation_status', '!=', 'completed')
            ->orderBy('severity', 'desc')
            ->limit(5)
            ->get();

        return $weakTopics->map(function ($weakTopic) {
            $topic = $weakTopic->topic;

            return [
                'weak_topic_id' => $weakTopic->id,
                'topic_id' => $topic->id,
                'topic_name' => $topic->name,
                'subject' => $topic->subject->name,
                'accuracy' => $weakTopic->accuracy,
                'severity' => $weakTopic->severity,
                'recommendation' => $this->generateRecommendation($weakTopic),
                'suggested_action' => "/learning/practice/topic/{$topic->id}?difficulty=medium",
                'practice_count' => (int)ceil((100 - $weakTopic->accuracy) / 5),
            ];
        })->toArray();
    }

    /**
     * Generate recommendation message
     */
    private function generateRecommendation(UserWeakTopic $weakTopic): string
    {
        $accuracy = $weakTopic->accuracy;
        $severity = $weakTopic->severity;

        if ($severity === 'critical') {
            return "You're struggling with {$weakTopic->topic->name}. Practice at least 10 more questions to improve your understanding.";
        } elseif ($severity === 'high') {
            return "You need to strengthen your concept in {$weakTopic->topic->name}. Try solving more questions from this topic.";
        } else {
            return "Review the concepts in {$weakTopic->topic->name} to improve your accuracy from {$accuracy}%.";
        }
    }

    /**
     * Check if topic is weak
     */
    public function isWeakTopic(int $userId, int $topicId): bool
    {
        return UserWeakTopic::where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->exists();
    }

    /**
     * Get weak topics trend
     */
    public function getWeakTopicsTrend(int $userId, int $daysAgo = 30): array
    {
        $startDate = now()->subDays($daysAgo);

        $trend = DB::select("
            SELECT
                DATE(uwt.detected_at) as date,
                COUNT(*) as new_weak_topics
            FROM user_weak_topics uwt
            WHERE uwt.user_id = ? AND uwt.detected_at >= ?
            GROUP BY DATE(uwt.detected_at)
            ORDER BY date ASC
        ", [$userId, $startDate]);

        return collect($trend)
            ->map(fn($item) => [
                'date' => $item->date,
                'count' => $item->new_weak_topics,
            ])
            ->toArray();
    }
}
