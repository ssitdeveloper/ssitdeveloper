<?php

namespace App\Services;

use App\Models\LearningQuestionAttempt;
use App\Models\LearningSession;
use App\Models\SessionQuestionHistory;
use App\Models\UserWeakTopic;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class AnswerCheckingService
{
    /**
     * Submit and check answer
     */
    public function submitAnswer(
        LearningSession $session,
        int $questionId,
        int $selectedAnswerId,
        int $timeSpentSeconds = 0
    ): array {
        $question = Question::findOrFail($questionId);

        // Get the correct answer
        $correctAnswer = $question->options()
            ->where('is_correct', true)
            ->first();

        $isCorrect = $correctAnswer && $correctAnswer->id === $selectedAnswerId;
        $marksObtained = $isCorrect ? $question->marks : -$question->negative_marks;

        // Record the attempt
        $attempt = LearningQuestionAttempt::create([
            'session_id' => $session->id,
            'question_id' => $questionId,
            'selected_option_id' => $selectedAnswerId,
            'is_correct' => $isCorrect,
            'marks_obtained' => $isCorrect ? $question->marks : -$question->negative_marks,
            'time_taken' => $timeSpentSeconds,
            'attempt_number' => 1,
        ]);

        // Update session statistics
        $session->increment('questions_completed');
        if ($isCorrect) {
            $session->increment('correct_answers');
        }
        $session->save();

        // Get selected and correct answer details
        $selectedOption = $question->options()->find($selectedAnswerId);
        $correctOption = $correctAnswer;

        return [
            'question_id' => $questionId,
            'selected_answer_id' => $selectedAnswerId,
            'is_correct' => $isCorrect,
            'correct_answer_id' => $correctOption?->id,
            'marks_obtained' => $marksObtained,
            'feedback' => $this->generateFeedback($isCorrect, $selectedOption, $correctOption),
            'session' => [
                'answered_questions' => $session->questions_completed,
                'correct_answers' => $session->correct_answers,
                'progress_percentage' => $session->getProgressPercentage(),
            ]
        ];
    }

    /**
     * Generate feedback message
     */
    private function generateFeedback($isCorrect, $selectedOption, $correctOption): string
    {
        if ($isCorrect) {
            return 'Excellent! You selected the correct answer.';
        }

        $selectedText = $selectedOption?->option_text ?? 'N/A';
        $correctText = $correctOption?->option_text ?? 'N/A';

        return "Incorrect. You selected: {$selectedText}. The correct answer is: {$correctText}";
    }

    /**
     * Get attempts for a session
     */
    public function getAttempts(LearningSession $session): array
    {
        return $session->attempts()
            ->with(['question', 'selectedOption'])
            ->get()
            ->map(function ($attempt) {
                return [
                    'question_id' => $attempt->question_id,
                    'question_text' => $attempt->question->question_text,
                    'selected_answer' => $attempt->selectedOption?->option_text,
                    'correct_answer' => $attempt->question->options()
                        ->where('is_correct', true)
                        ->first()?->option_text,
                    'is_correct' => $attempt->is_correct,
                    'marks' => $attempt->marks_obtained,
                    'time_taken' => $attempt->time_taken,
                ];
            })
            ->toArray();
    }

    /**
     * Validate answer is for current session
     */
    public function validateAnswer(LearningSession $session, int $questionId): bool
    {
        return $session->questionHistory()
            ->where('question_id', $questionId)
            ->exists();
    }

    /**
     * Get answer statistics
     */
    public function getAnswerStats(LearningSession $session): array
    {
        $attempts = $session->attempts()->get();

        return [
            'total_attempts' => $attempts->count(),
            'correct' => $attempts->where('is_correct', true)->count(),
            'incorrect' => $attempts->where('is_correct', false)->count(),
            'total_marks' => $attempts->sum('marks_obtained'),
            'avg_time_per_answer' => $attempts->count() > 0
                ? round($attempts->sum('time_taken') / $attempts->count(), 2)
                : 0,
        ];
    }

    /**
     * Review answers
     */
    public function reviewAnswers(LearningSession $session): array
    {
        return $this->getAttempts($session);
    }
}
