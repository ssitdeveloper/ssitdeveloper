<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeakTopicMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public array $weakTopics = []
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recommended Topics to Focus On - NEET LMS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $topicRecommendations = [];

        // Get weak topics from analytics if not provided
        if (empty($this->weakTopics)) {
            $analytics = $this->user->analytics;
            $subject_accuracies = json_decode($analytics?->subject_wise_accuracy ?? '{}', true);

            foreach ($subject_accuracies as $subject_id => $accuracy) {
                if ($accuracy < 70) { // Focus on topics with < 70% accuracy
                    $topicRecommendations[] = [
                        'subject' => \App\Models\Subject::find($subject_id),
                        'accuracy' => round($accuracy, 2),
                        'difficulty' => $accuracy < 40 ? 'EASY' : 'MEDIUM',
                    ];
                }
            }
        } else {
            $topicRecommendations = $this->weakTopics;
        }

        // Sort by accuracy (lowest first)
        usort($topicRecommendations, fn($a, $b) => $a['accuracy'] <=> $b['accuracy']);

        // Limit to top 5
        $topicRecommendations = array_slice($topicRecommendations, 0, 5);

        return new Content(
            markdown: 'emails.weak-topic',
            with: [
                'studentName' => $this->user->name,
                'weakTopics' => $topicRecommendations,
                'topicsCount' => count($topicRecommendations),
                'practiceUrl' => route('question-bank.practice'),
                'dashboardUrl' => route('analytics.dashboard'),
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
