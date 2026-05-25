<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analyticsService)
    {
        $user = auth()->user();
        $stats = $analyticsService->getUserDashboardStats($user);
        $subjectWiseAnalytics = $analyticsService->getSubjectWiseAnalytics($user);

        return view('student.analytics', [
            'stats' => $stats,
            'subjectWiseAnalytics' => $subjectWiseAnalytics,
        ]);
    }

    public function leaderboard(AnalyticsService $analyticsService)
    {
        $leaderboard = $analyticsService->getLeaderboard(20);

        return view('student.leaderboard', ['leaderboard' => $leaderboard]);
    }

    public function weakTopics()
    {
        $weakTopics = \App\Models\UserWeakTopic::where('user_id', auth()->id())
            ->with('topic.subject', 'topic.chapters.questions')
            ->orderBy('weak_score', 'asc')
            ->paginate(15);

        return view('student.analytics.weak-topics', ['weakTopics' => $weakTopics]);
    }

    public function progress()
    {
        $user = auth()->user();

        $learningProgress = $user->learningProgress()
            ->with('chapter.topic.subject')
            ->latest()
            ->paginate(20);

        $stats = [
            'chapters_started' => $learningProgress->where('progress', '>', 0)->count(),
            'chapters_completed' => $learningProgress->where('progress', '=', 100)->count(),
            'avg_progress' => round($learningProgress->avg('progress') ?? 0),
        ];

        return view('student.analytics.progress', [
            'learningProgress' => $learningProgress,
            'stats' => $stats,
        ]);
    }

    public function testHistory()
    {
        $attempts = auth()->user()->testAttempts()
            ->with('test')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_tests' => $attempts->total(),
            'passed' => $attempts->where('status', 'passed')->count(),
            'avg_score' => round($attempts->avg('marks_obtained') ?? 0),
        ];

        return view('student.analytics.test-history', [
            'attempts' => $attempts,
            'stats' => $stats,
        ]);
    }

    /**
     * Comprehensive analytics dashboard
     */
    public function dashboard(AnalyticsService $analyticsService)
    {
        $user = auth()->user();

        // Overall stats
        $overall_stats = [
            'total_tests_taken' => $user->testAttempts()->where('status', 'completed')->count(),
            'accuracy_percentage' => $user->analytics?->accuracy ?? 0,
            'study_streak_days' => $user->analytics?->study_streak ?? 0,
            'total_questions_practiced' => $user->testAttempts()->sum('total_questions') ?? 0,
        ];

        // Accuracy trend (last 10 tests)
        $accuracy_trend = $user->testAttempts()
            ->where('status', 'completed')
            ->orderBy('created_at', 'asc')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($attempt) => [
                'date' => $attempt->completed_at->format('M d'),
                'score' => $attempt->score,
            ]);

        // Weak subjects (ordered by lowest accuracy)
        $subject_accuracies = json_decode($user->analytics?->subject_wise_accuracy ?? '{}', true);
        $weak_subjects = collect($subject_accuracies)
            ->sortBy(fn($accuracy) => $accuracy)
            ->slice(0, 5)
            ->map(function ($accuracy, $subject_id) {
                $subject = \App\Models\Subject::find($subject_id);
                return [
                    'subject' => $subject,
                    'accuracy' => $accuracy,
                    'status' => $accuracy < 50 ? 'critical' : ($accuracy < 75 ? 'warning' : 'good'),
                ];
            });

        // Strong subjects
        $strong_subjects = collect($subject_accuracies)
            ->sortByDesc(fn($accuracy) => $accuracy)
            ->slice(0, 3)
            ->map(function ($accuracy, $subject_id) {
                $subject = \App\Models\Subject::find($subject_id);
                return [
                    'subject' => $subject,
                    'accuracy' => $accuracy,
                ];
            });

        // Your rank vs class (if leaderboard enabled)
        $your_rank = null;
        $class_average = 0;
        try {
            $leaderboard = $analyticsService->getLeaderboard(1000);
            $your_rank = collect($leaderboard)->search(fn($item) => $item['user_id'] === $user->id);
            $class_average = $leaderboard->avg('score') ?? 0;
        } catch (\Exception $e) {
            // Leaderboard not available
        }

        return view('student.analytics.dashboard', compact(
            'overall_stats',
            'accuracy_trend',
            'weak_subjects',
            'strong_subjects',
            'your_rank',
            'class_average'
        ));
    }

    /**
     * Subject-wise deep dive
     */
    public function subjectDetail(\App\Models\Subject $subject, AnalyticsService $analyticsService)
    {
        $user = auth()->user();

        // Get all attempts with this subject's questions
        $subject_attempts = $user->testAttempts()
            ->with('test', 'answers.question')
            ->get()
            ->filter(function ($attempt) use ($subject) {
                return $attempt->answers->contains(fn($answer) =>
                    $answer->question->chapter?->topic?->subject_id === $subject->id
                );
            });

        // Chapter-wise breakdown
        $chapters = $subject->chapters()
            ->with('questions')
            ->get()
            ->map(function ($chapter) use ($user, $subject_attempts) {
                $chapter_questions = $chapter->questions->pluck('id');

                $correct_answers = 0;
                $total_answers = 0;

                foreach ($subject_attempts as $attempt) {
                    foreach ($attempt->answers as $answer) {
                        if ($chapter_questions->contains($answer->question_id)) {
                            $total_answers++;
                            if ($answer->isCorrect()) {
                                $correct_answers++;
                            }
                        }
                    }
                }

                return [
                    'chapter' => $chapter,
                    'accuracy' => $total_answers > 0 ? ($correct_answers / $total_answers) * 100 : 0,
                    'attempts' => $total_answers,
                ];
            })
            ->sortByDesc('accuracy');

        $stats = [
            'total_attempts_with_subject' => $subject_attempts->count(),
            'overall_accuracy' => $chapters->avg('accuracy'),
            'weakest_chapter' => $chapters->last(),
            'strongest_chapter' => $chapters->first(),
        ];

        return view('student.analytics.subject-detail', compact('subject', 'chapters', 'stats'));
    }

    /**
     * Performance insights & recommendations
     */
    public function insights()
    {
        $user = auth()->user();
        $analytics = $user->analytics;

        $insights = [];

        if (!$analytics) {
            return view('student.analytics.insights', compact('insights'));
        }

        // Insight 1: Study consistency
        if ($analytics->study_streak >= 7) {
            $insights[] = [
                'type' => 'positive',
                'icon' => 'fire',
                'title' => 'Great Consistency!',
                'message' => "You've been consistent for {$analytics->study_streak} days in a row!",
            ];
        } elseif ($analytics->study_streak >= 3) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'trending-up',
                'title' => 'Building Momentum',
                'message' => "You have a {$analytics->study_streak} day streak. Keep it up!",
            ];
        }

        // Insight 2: Accuracy trend
        $recent_attempts = $user->testAttempts()
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($recent_attempts->count() >= 2) {
            $recent_avg = $recent_attempts->avg('score');
            $prev_avg = $user->testAttempts()
                ->where('created_at', '<', $recent_attempts->last()->created_at)
                ->where('status', 'completed')
                ->limit(5)
                ->avg('score');

            if ($recent_avg > $prev_avg) {
                $improvement = round($recent_avg - $prev_avg, 2);
                $insights[] = [
                    'type' => 'positive',
                    'icon' => 'arrow-up',
                    'title' => 'Improving Performance',
                    'message' => "Your recent tests show +{$improvement}% improvement!",
                ];
            }
        }

        // Insight 3: Weak topics
        $subject_accuracies = json_decode($analytics->subject_wise_accuracy ?? '{}', true);
        $weakest_subject_id = array_key_first($subject_accuracies);
        if ($weakest_subject_id && $subject_accuracies[$weakest_subject_id] < 60) {
            $subject = \App\Models\Subject::find($weakest_subject_id);
            $insights[] = [
                'type' => 'warning',
                'icon' => 'alert-circle',
                'title' => 'Focus Area Needed',
                'message' => "You're struggling with {$subject->name}. Consider revising this topic.",
                'action' => route('results.recommendations'),
                'action_label' => 'View Practice Questions',
            ];
        }

        return view('student.analytics.insights', compact('insights'));
    }

    /**
     * Leaderboard with detailed rankings
     */
    public function leaderboardDetailed(AnalyticsService $analyticsService)
    {
        $user = auth()->user();
        $leaderboard = $analyticsService->getLeaderboard(100);

        // Find user's rank and position
        $user_rank = collect($leaderboard)->search(fn($item) => $item['user_id'] === $user->id);
        $percentile = $user_rank ? (($user_rank + 1) / count($leaderboard)) * 100 : 0;

        // Get ranking segments for context
        $top_10 = collect($leaderboard)->slice(0, 10);
        $user_segment = collect($leaderboard)->slice(max(0, $user_rank - 5), 10);

        return view('student.analytics.leaderboard-detailed', compact(
            'leaderboard',
            'user_rank',
            'percentile',
            'top_10',
            'user_segment',
            'user'
        ));
    }
}
