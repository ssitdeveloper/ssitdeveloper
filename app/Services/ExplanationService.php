<?php

namespace App\Services;

use App\Models\LearningExplanation;
use App\Models\Question;

class ExplanationService
{
    /**
     * Get explanation for a question
     */
    public function getExplanation(int $questionId): array
    {
        $question = Question::with('explanation')->findOrFail($questionId);

        $explanation = $question->explanation ?? $this->generateBasicExplanation($question);

        return [
            'question_id' => $questionId,
            'short_explanation' => $explanation->short_explanation ?? 'Explanation not available',
            'detailed_explanation' => $explanation->detailed_explanation ?? '',
            'related_concepts' => $this->parseRelatedConcepts($explanation->related_concepts ?? ''),
            'mnemonics' => $explanation->mnemonics ?? null,
            'video_url' => $explanation->video_url ?? null,
            'reference_material' => $this->parseReferences($explanation->reference_material ?? ''),
        ];
    }

    /**
     * Generate basic explanation if not available
     */
    private function generateBasicExplanation(Question $question): ?LearningExplanation
    {
        $correctOption = $question->options()
            ->where('is_correct', true)
            ->first();

        return new LearningExplanation([
            'question_id' => $question->id,
            'short_explanation' => "The correct answer is: {$correctOption?->option_text}",
            'detailed_explanation' => '',
            'related_concepts' => null,
            'mnemonics' => null,
            'video_url' => null,
            'reference_material' => null,
        ]);
    }

    /**
     * Parse related concepts from string
     */
    private function parseRelatedConcepts(?string $concepts): array
    {
        if (!$concepts) {
            return [];
        }

        return array_map('trim', explode(',', $concepts));
    }

    /**
     * Parse reference material
     */
    private function parseReferences(?string $material): array
    {
        if (!$material) {
            return [];
        }

        return array_map('trim', explode('|', $material));
    }

    /**
     * Store or update explanation
     */
    public function storeExplanation(int $questionId, array $data): LearningExplanation
    {
        return LearningExplanation::updateOrCreate(
            ['question_id' => $questionId],
            [
                'short_explanation' => $data['short_explanation'] ?? null,
                'detailed_explanation' => $data['detailed_explanation'] ?? null,
                'related_concepts' => is_array($data['related_concepts'] ?? null)
                    ? implode(',', $data['related_concepts'])
                    : $data['related_concepts'],
                'mnemonics' => $data['mnemonics'] ?? null,
                'video_url' => $data['video_url'] ?? null,
                'reference_material' => is_array($data['reference_material'] ?? null)
                    ? implode('|', $data['reference_material'])
                    : $data['reference_material'],
            ]
        );
    }

    /**
     * Get explanations for multiple questions
     */
    public function getMultipleExplanations(array $questionIds): array
    {
        return Question::whereIn('id', $questionIds)
            ->with('explanation')
            ->get()
            ->map(fn($question) => $this->getExplanation($question->id))
            ->toArray();
    }

    /**
     * Check if explanation exists
     */
    public function hasExplanation(int $questionId): bool
    {
        return LearningExplanation::where('question_id', $questionId)->exists();
    }

    /**
     * Get explanation coverage stats
     */
    public function getExplanationCoverageStats(int $subjectId): array
    {
        $totalQuestions = Question::where('subject_id', $subjectId)->count();
        $questionsWithExplanation = Question::where('subject_id', $subjectId)
            ->whereHas('explanation')
            ->count();

        return [
            'total_questions' => $totalQuestions,
            'with_explanation' => $questionsWithExplanation,
            'without_explanation' => $totalQuestions - $questionsWithExplanation,
            'coverage_percentage' => $totalQuestions > 0
                ? round(($questionsWithExplanation / $totalQuestions) * 100, 2)
                : 0,
        ];
    }
}
