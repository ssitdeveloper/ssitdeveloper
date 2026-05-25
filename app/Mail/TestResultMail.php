<?php

namespace App\Mail;

use App\Models\TestAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestResultMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public TestAttempt $attempt)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Test Results: {$this->attempt->test->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Calculate basic stats
        $total_questions = count(json_decode($this->attempt->question_ids, true) ?? []);
        $total_answered = $this->attempt->answers()->count();
        $accuracy = $total_answered > 0 ? ($this->attempt->score / 100) : 0;

        return new Content(
            markdown: 'emails.test-result',
            with: [
                'studentName' => $this->attempt->user->name,
                'testName' => $this->attempt->test->title,
                'score' => $this->attempt->score ?? 0,
                'totalQuestions' => $total_questions,
                'questionsAnswered' => $total_answered,
                'questionsSkipped' => $total_questions - $total_answered,
                'accuracy' => round($accuracy * 100, 2),
                'duration' => $this->attempt->submitted_at && $this->attempt->started_at
                    ? $this->attempt->submitted_at->diffInMinutes($this->attempt->started_at)
                    : 0,
                'resultUrl' => route('results.show', $this->attempt),
                'reviewUrl' => route('results.review', $this->attempt),
                'recommendationsUrl' => route('results.recommendations', $this->attempt),
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
