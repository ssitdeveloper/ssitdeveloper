<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Bookmark;
use App\Models\User;
use App\Enums\DifficultyLevel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    /**
     * Get paginated questions with filters
     */
    public function getQuestions(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Question::with(['chapter.topic.subject', 'options', 'correctOption'])
            ->published();

        // Apply filters
        if (!empty($filters['subject_id'])) {
            $query->bySubject($filters['subject_id']);
        }

        if (!empty($filters['topic_id'])) {
            $query->byTopic($filters['topic_id']);
        }

        if (!empty($filters['chapter_id'])) {
            $query->byChapter($filters['chapter_id']);
        }

        if (!empty($filters['difficulty'])) {
            $query->difficulty(DifficultyLevel::from($filters['difficulty']));
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if ($orderBy === 'random') {
            $query->inRandomOrder();
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get questions for a specific chapter
     */
    public function getQuestionsForChapter(int $chapterId, int $limit = 10, int $page = 1): LengthAwarePaginator
    {
        return Question::where('chapter_id', $chapterId)
            ->published()
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Get questions by chapter with additional filters
     */
    public function getQuestionsByChapter(int $chapterId, array $filters = []): Collection
    {
        $query = Question::with(['options', 'correctOption'])
            ->byChapter($chapterId)
            ->published();

        if (!empty($filters['difficulty'])) {
            $query->difficulty(DifficultyLevel::from($filters['difficulty']));
        }

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        if (!empty($filters['random'])) {
            $query->inRandomOrder();
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    /**
     * Get random questions for practice
     */
    public function getRandomQuestions(int $count = 10, array $filters = []): Collection
    {
        $query = Question::with(['chapter.topic.subject', 'options', 'correctOption'])
            ->published();

        // Apply filters
        if (!empty($filters['subject_id'])) {
            $query->bySubject($filters['subject_id']);
        }

        if (!empty($filters['topic_id'])) {
            $query->byTopic($filters['topic_id']);
        }

        if (!empty($filters['chapter_id'])) {
            $query->byChapter($filters['chapter_id']);
        }

        if (!empty($filters['difficulty'])) {
            $query->difficulty(DifficultyLevel::from($filters['difficulty']));
        }

        return $query->inRandomOrder()->limit($count)->get();
    }

    /**
     * Search questions
     */
    public function searchQuestions(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Question::with(['chapter.topic.subject', 'options', 'correctOption'])
            ->published()
            ->search($query);

        // Apply additional filters
        if (!empty($filters['subject_id'])) {
            $searchQuery->bySubject($filters['subject_id']);
        }

        if (!empty($filters['difficulty'])) {
            $searchQuery->difficulty(DifficultyLevel::from($filters['difficulty']));
        }

        return $searchQuery->paginate($perPage);
    }

    /**
     * Get questions by difficulty
     */
    public function getQuestionsByDifficulty(string $difficulty, int $limit = 10): LengthAwarePaginator
    {
        return Question::difficulty(DifficultyLevel::from($difficulty))
            ->published()
            ->paginate($limit);
    }

    /**
     * Get question by ID with full details
     */
    public function getQuestionById(int $questionId): ?Question
    {
        return Question::with(['chapter.topic.subject', 'options', 'correctOption'])
            ->find($questionId);
    }

    /**
     * Record question view
     */
    public function recordQuestionView(Question $question): void
    {
        $question->incrementViews();
    }

    /**
     * Record question attempt
     */
    public function recordQuestionAttempt(Question $question): void
    {
        $question->incrementAttempts();
    }

    /**
     * Validate answer for a question
     */
    public function validateAnswer(int $questionId, int $selectedOptionId): array
    {
        $question = $this->getQuestionById($questionId);

        if (!$question) {
            return [
                'is_correct' => false,
                'message' => 'Question not found',
                'correct_option' => null,
                'explanation' => null,
            ];
        }

        $selectedOption = $question->options()->find($selectedOptionId);
        $correctOption = $question->getCorrectOption();

        $isCorrect = $selectedOption && $selectedOption->is_correct;

        // Record attempt
        $this->recordQuestionAttempt($question);

        return [
            'is_correct' => $isCorrect,
            'message' => $isCorrect ? 'Correct answer!' : 'Incorrect answer',
            'correct_option' => $correctOption,
            'selected_option' => $selectedOption,
            'explanation' => $correctOption?->explanation,
        ];
    }

    /**
     * Bookmark management
     */
    public function bookmarkQuestion(User $user, Question $question): bool
    {
        return (bool) $user->bookmarks()->syncWithoutDetaching([$question->id]);
    }

    public function removeBookmark(User $user, Question $question): int
    {
        return $user->bookmarks()->detach($question->id);
    }

    public function getUserBookmarks(User $user, int $limit = 10): LengthAwarePaginator
    {
        return $user->bookmarks()
            ->with(['question.chapter.topic.subject'])
            ->latest('bookmarks.created_at')
            ->paginate($limit);
    }

    /**
     * Get question statistics
     */
    public function getQuestionStats(int $questionId): array
    {
        $question = Question::find($questionId);

        if (!$question) {
            return [];
        }

        $totalAttempts = $question->answers()->count();
        $correctAttempts = $question->answers()->where('is_correct', true)->count();
        $accuracy = $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100, 2) : 0;

        return [
            'question_id' => $questionId,
            'views_count' => $question->views_count,
            'attempts_count' => $question->attempts_count,
            'total_answers' => $totalAttempts,
            'correct_answers' => $correctAttempts,
            'accuracy_percentage' => $accuracy,
        ];
    }

    /**
     * Get questions statistics by filters
     */
    public function getQuestionsStats(array $filters = []): array
    {
        $query = Question::query();

        if (!empty($filters['subject_id'])) {
            $query->bySubject($filters['subject_id']);
        }

        if (!empty($filters['topic_id'])) {
            $query->byTopic($filters['topic_id']);
        }

        if (!empty($filters['chapter_id'])) {
            $query->byChapter($filters['chapter_id']);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_questions,
            SUM(views_count) as total_views,
            SUM(attempts_count) as total_attempts,
            AVG(CASE WHEN attempts_count > 0 THEN (views_count * 1.0 / attempts_count) ELSE 0 END) as avg_views_per_attempt
        ')->first();

        return [
            'total_questions' => $stats->total_questions ?? 0,
            'total_views' => $stats->total_views ?? 0,
            'total_attempts' => $stats->total_attempts ?? 0,
            'avg_views_per_attempt' => round($stats->avg_views_per_attempt ?? 0, 2),
        ];
    }
}
