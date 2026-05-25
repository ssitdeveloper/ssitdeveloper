<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PerformanceInsightsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Weekly Performance Insights - NEET LMS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $user = $this->user;

        // Calculate week stats
        $week_start = now()->subWeek();
        $week_attempts = $user->testAttempts()
            ->where('status', 'completed')
            ->where('created_at', '>=', $week_start)
            ->get();

        $week_stats = [
            'tests_taken' => $week_attempts->count(),
            'avg_score' => round($week_attempts->avg('score') ?? 0, 2),
            'improvement' => 0, // Calculate vs previous week
        ];

        // Get weak topics for this week
        $weak_topics = [];
        $subject_stats = [];

        foreach ($week_attempts as $attempt) {
            foreach ($attempt->answers as $answer) {
                $question = $answer->question;
                $subject = $question->chapter?->topic?->subject;

                if ($subject) {
                    if (!isset($subject_stats[$subject->id])) {
                        $subject_stats[$subject->id] = [
                            'subject' => $subject,
                            'correct' => 0,
                            'total' => 0,
                        ];
                    }

                    $subject_stats[$subject->id]['total']++;
                    if ($answer->isCorrect() ?? false) {
                        $subject_stats[$subject->id]['correct']++;
                    }
                }
            }
        }

        // Sort by accuracy
        usort($subject_stats, fn($a, $b) =>
            (($a['correct'] / max(1, $a['total'])) <=> ($b['correct'] / max(1, $b['total'])))
        );

        // Get top 3 weak subjects
        $weak_topics = array_slice($subject_stats, 0, 3);

        // Calculate study streak
        $study_streak = $user->analytics?->study_streak ?? 0;

        return new Content(
            markdown: 'emails.performance-insights',
            with: [
                'studentName' => $user->name,
                'weekStats' => $week_stats,
                'weakTopics' => $weak_topics,
                'studyStreak' => $study_streak,
                'dashboardUrl' => route('analytics.dashboard'),
                'insightsUrl' => route('analytics.insights'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
