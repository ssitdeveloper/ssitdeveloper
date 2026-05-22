<?php

namespace App\Services;

use App\Models\LearningRecommendation;
use App\Models\UserWeakTopic;
use App\Models\Topic;
use App\Models\User;
use App\Models\LearningSession;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecommendationEngine
{
    /**
     * Generate recommendations for a user
     */
    public function generateRecommendations(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Clear old recommendations
        LearningRecommendation::where('user_id', $userId)
            ->where('expires_at', '<', now())
            ->delete();

        // Generate weak topic recommendations
        $this->generateWeakTopicRecommendations($userId);

        // Generate strength recommendations
        $this->generateStrengthRecommendations($userId);

        // Generate practice recommendations
        $this->generatePracticeRecommendations($userId);

        // Generate revision recommendations
        $this->generateRevisionRecommendations($userId);
    }

    /**
     * Generate weak topic recommendations
     */
    private function generateWeakTopicRecommendations(int $userId): void
    {
        $weakTopics = UserWeakTopic::where('user_id', $userId)
            ->where('severity', '!=', 'medium')
            ->get();

        foreach ($weakTopics as $weakTopic) {
            LearningRecommendation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'recommendation_type' => 'weak_topic',
                    'topic_id' => $weakTopic->topic_id,
                ],
                [
                    'subject_id' => $weakTopic->subject_id,
                    'priority' => $this->determinePriority($weakTopic->severity),
                    'reason' => "Your accuracy in {$weakTopic->topic->name} is {$weakTopic->accuracy}%",
                    'recommendation_text' => "Practice more on {$weakTopic->topic->name}",
                    'action_url' => "/learning/practice/topic/{$weakTopic->topic_id}?difficulty=easy",
                    'is_active' => true,
                    'expires_at' => now()->addDays(7),
                ]
            );
        }
    }

    /**
     * Generate strength recommendations (advanced questions)
     */
    private function generateStrengthRecommendations(int $userId): void
    {
        $strongTopics = DB::select("
            SELECT
                t.id as topic_id,
                s.id as subject_id,
                t.name as topic_name,
                ROUND(AVG(ls.accuracy_percentage), 2) as accuracy
            FROM learning_sessions ls
            JOIN topics t ON ls.topic_id = t.id
            JOIN subjects s ON ls.subject_id = s.id
            WHERE ls.user_id = ?
            AND ls.accuracy_percentage >= 80
            GROUP BY t.id, s.id
            LIMIT 3
        ", [$userId]);

        foreach ($strongTopics as $topic) {
            LearningRecommendation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'recommendation_type' => 'strength',
                    'topic_id' => (int)$topic->topic_id,
                ],
                [
                    'subject_id' => (int)$topic->subject_id,
                    'priority' => 'medium',
                    'reason' => "You have achieved {$topic->accuracy}% accuracy in this topic",
                    'recommendation_text' => "Try advanced questions in {$topic->topic_name}",
                    'action_url' => "/learning/practice/topic/{$topic->topic_id}?difficulty=hard",
                    'is_active' => true,
                    'expires_at' => now()->addDays(14),
                ]
            );
        }
    }

    /**
     * Generate practice recommendations based on recent attempts
     */
    private function generatePracticeRecommendations(int $userId): void
    {
        $leastAttempted = DB::select("
            SELECT
                t.id as topic_id,
                s.id as subject_id,
                t.name as topic_name,
                COUNT(ls.id) as session_count
            FROM topics t
            JOIN subjects s ON t.subject_id = s.id
            LEFT JOIN learning_sessions ls ON t.id = ls.topic_id AND ls.user_id = ?
            WHERE s.is_active = 1 AND t.is_active = 1
            GROUP BY t.id, s.id
            HAVING session_count < 3
            ORDER BY session_count ASC, RAND()
            LIMIT 3
        ", [$userId]);

        foreach ($leastAttempted as $topic) {
            LearningRecommendation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'recommendation_type' => 'practice',
                    'topic_id' => (int)$topic->topic_id,
                ],
                [
                    'subject_id' => (int)$topic->subject_id,
                    'priority' => 'low',
                    'reason' => "You haven't practiced much in {$topic->topic_name}",
                    'recommendation_text' => "Start practicing {$topic->topic_name}",
                    'action_url' => "/learning/practice/topic/{$topic->topic_id}",
                    'is_active' => true,
                    'expires_at' => now()->addDays(7),
                ]
            );
        }
    }

    /**
     * Generate revision recommendations based on time since last attempt
     */
    private function generateRevisionRecommendations(int $userId): void
    {
        $oldTopics = DB::select("
            SELECT
                t.id as topic_id,
                s.id as subject_id,
                t.name as topic_name,
                MAX(ls.completed_at) as last_attempt
            FROM learning_sessions ls
            JOIN topics t ON ls.topic_id = t.id
            JOIN subjects s ON ls.subject_id = s.id
            WHERE ls.user_id = ?
            AND ls.is_completed = 1
            GROUP BY t.id, s.id
            HAVING last_attempt < DATE_SUB(NOW(), INTERVAL 14 DAY)
            LIMIT 3
        ", [$userId]);

        foreach ($oldTopics as $topic) {
            LearningRecommendation::updateOrCreate(
                [
                    'user_id' => $userId,
                    'recommendation_type' => 'revision',
                    'topic_id' => (int)$topic->topic_id,
                ],
                [
                    'subject_id' => (int)$topic->subject_id,
                    'priority' => 'medium',
                    'reason' => "You haven't revised {$topic->topic_name} in a while",
                    'recommendation_text' => "Revise {$topic->topic_name} to maintain retention",
                    'action_url' => "/learning/practice/topic/{$topic->topic_id}?difficulty=medium",
                    'is_active' => true,
                    'expires_at' => now()->addDays(7),
                ]
            );
        }
    }

    /**
     * Get active recommendations for a user
     */
    public function getRecommendations(int $userId, int $limit = 5): array
    {
        return LearningRecommendation::where('user_id', $userId)
            ->where('is_active', true)
            ->where('is_acknowledged', false)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByRaw("CASE
                WHEN priority = 'critical' THEN 1
                WHEN priority = 'high' THEN 2
                WHEN priority = 'medium' THEN 3
                ELSE 4
            END")
            ->limit($limit)
            ->get()
            ->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'type' => $rec->recommendation_type,
                    'subject' => $rec->subject?->name,
                    'topic' => $rec->topic?->name,
                    'priority' => $rec->priority,
                    'reason' => $rec->reason,
                    'recommendation_text' => $rec->recommendation_text,
                    'action_url' => $rec->action_url,
                ];
            })
            ->toArray();
    }

    /**
     * Acknowledge recommendation
     */
    public function acknowledgeRecommendation(int $recommendationId): void
    {
        LearningRecommendation::findOrFail($recommendationId)->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);
    }

    /**
     * Get personalized learning path
     */
    public function generateLearningPath(int $userId): array
    {
        $stats = DB::select("
            SELECT
                s.id,
                s.name,
                COUNT(DISTINCT ls.id) as session_count,
                ROUND(AVG(ls.accuracy_percentage), 2) as avg_accuracy
            FROM subjects s
            LEFT JOIN learning_sessions ls ON s.id = ls.subject_id AND ls.user_id = ?
            WHERE s.is_active = 1
            GROUP BY s.id, s.name
            ORDER BY avg_accuracy ASC
        ", [$userId]);

        return collect($stats)->map(function ($subject) {
            $percentage = $subject->avg_accuracy ?? 0;

            if ($percentage < 60) {
                $recommendation = "Focus on {$subject->name}";
                $action = "practice";
            } elseif ($percentage < 75) {
                $recommendation = "Improve in {$subject->name}";
                $action = "practice";
            } else {
                $recommendation = "Strengthen {$subject->name}";
                $action = "advanced";
            }

            return [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'current_accuracy' => $percentage,
                'session_count' => $subject->session_count,
                'recommendation' => $recommendation,
                'suggested_action' => $action,
                'priority' => $percentage < 60 ? 'high' : ($percentage < 75 ? 'medium' : 'low'),
            ];
        })->toArray();
    }

    /**
     * Determine priority based on severity
     */
    private function determinePriority(string $severity): string
    {
        return match($severity) {
            'critical' => 'critical',
            'high' => 'high',
            'medium' => 'medium',
            default => 'low',
        };
    }
}
