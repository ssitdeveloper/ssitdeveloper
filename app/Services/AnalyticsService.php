<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getOrCreateAnalytics(User $user): Analytics
    {
        return Analytics::firstOrCreate(['user_id' => $user->id]);
    }

    public function updateFromTestAttempt($user): void
    {
        $analytics = $this->getOrCreateAnalytics($user);

        $attempts = $user->testAttempts()->with(['answers' => function($q) {
            $q->with('selectedOption');
        }])->get();

        $totalAttempted = 0;
        $totalCorrect = 0;

        foreach ($attempts as $attempt) {
            foreach ($attempt->answers as $answer) {
                $totalAttempted++;
                if ($answer->selectedOption && $answer->selectedOption->is_correct) {
                    $totalCorrect++;
                }
            }
        }

        $accuracy = $totalAttempted > 0 ? ($totalCorrect / $totalAttempted) * 100 : 0;

        $analytics->update([
            'total_questions_attempted' => $totalAttempted,
            'total_tests_taken' => $attempts->count(),
            'accuracy_percentage' => $accuracy,
        ]);
    }

    public function getUserDashboardStats(User $user): array
    {
        $analytics = $this->getOrCreateAnalytics($user);
        $recentAttempts = $user->testAttempts()
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        return [
            'total_questions' => $analytics->total_questions_attempted,
            'total_tests' => $analytics->total_tests_taken,
            'accuracy' => $analytics->accuracy_percentage,
            'study_streak' => $analytics->study_streak_days,
            'study_minutes' => $analytics->total_study_minutes,
            'recent_tests' => $recentAttempts,
        ];
    }

    public function getLeaderboard(int $limit = 10): array
    {
        return Analytics::with('user')
            ->orderBy('accuracy_percentage', 'desc')
            ->orderBy('total_tests_taken', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getSubjectWiseAnalytics(User $user): array
    {
        $subjectStats = DB::table('questions')
            ->join('chapters', 'questions.chapter_id', '=', 'chapters.id')
            ->join('topics', 'chapters.topic_id', '=', 'topics.id')
            ->join('subjects', 'topics.subject_id', '=', 'subjects.id')
            ->join('test_attempt_answers', 'questions.id', '=', 'test_attempt_answers.question_id')
            ->join('test_attempts', 'test_attempt_answers.test_attempt_id', '=', 'test_attempts.id')
            ->join('options', 'test_attempt_answers.selected_option_id', '=', 'options.id')
            ->where('test_attempts.user_id', $user->id)
            ->select('subjects.name', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN options.is_correct THEN 1 ELSE 0 END) as correct'))
            ->groupBy('subjects.id', 'subjects.name')
            ->get();

        return $subjectStats->map(fn($stat) => [
            'subject' => $stat->name,
            'total' => $stat->total,
            'correct' => $stat->correct,
            'accuracy' => $stat->total > 0 ? ($stat->correct / $stat->total) * 100 : 0,
        ])->toArray();
    }
}
