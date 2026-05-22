<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tests table - defines test templates
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->integer('duration_minutes'); // Test duration
            $table->integer('total_questions');
            $table->json('subject_distribution'); // e.g., {"physics": 45, "chemistry": 45, "biology": 90}
            $table->json('difficulty_distribution'); // e.g., {"easy": 30, "medium": 50, "hard": 20}
            $table->decimal('negative_marking', 3, 2)->default(0.25); // 0.25 marks deducted for wrong answer
            $table->decimal('marks_per_question', 3, 2)->default(4.00);
            $table->boolean('is_active')->default(true);
            $table->timestamp('scheduled_at')->nullable(); // For scheduled tests
            $table->timestamp('expires_at')->nullable(); // When test becomes unavailable
            $table->json('instructions')->nullable(); // Test instructions
            $table->timestamps();

            $table->index(['is_active', 'scheduled_at']);
        });

        // Test attempts table - tracks user attempts
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('question_ids'); // Randomized question IDs for this attempt
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // When the test should auto-submit
            $table->enum('status', ['in_progress', 'completed', 'expired', 'abandoned'])->default('in_progress');
            $table->integer('time_remaining_seconds')->nullable(); // For resume functionality
            $table->json('metadata')->nullable(); // Additional data like IP, browser info
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['test_id', 'status']);
            $table->unique(['test_id', 'user_id', 'started_at']); // Prevent duplicate attempts
        });

        // Test attempt answers table - individual answers
        Schema::create('test_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('options')->onDelete('set null');
            $table->text('answer_text')->nullable(); // For subjective questions if needed
            $table->boolean('is_marked_for_review')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['test_attempt_id', 'question_id']);
            $table->index(['test_attempt_id', 'is_marked_for_review']);
        });

        // Test results table - final results and analytics
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained()->onDelete('cascade');
            $table->integer('total_questions');
            $table->integer('attempted_questions');
            $table->integer('correct_answers');
            $table->integer('wrong_answers');
            $table->integer('unanswered_questions');
            $table->decimal('total_marks', 8, 2);
            $table->decimal('obtained_marks', 8, 2);
            $table->decimal('negative_marks', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2);
            $table->integer('rank')->nullable(); // Overall rank in this test
            $table->decimal('percentile', 5, 2)->nullable();
            $table->json('subject_wise_analysis')->nullable(); // Performance by subject
            $table->json('difficulty_wise_analysis')->nullable(); // Performance by difficulty
            $table->json('time_wise_analysis')->nullable(); // Time spent analysis
            $table->text('recommendations')->nullable(); // AI-generated recommendations
            $table->timestamps();

            $table->index(['test_attempt_id']);
            $table->index(['percentage', 'rank']);
        });

        // Test analytics table - for percentile calculations
        Schema::create('test_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->decimal('score_range_min', 8, 2);
            $table->decimal('score_range_max', 8, 2);
            $table->integer('student_count');
            $table->decimal('percentile', 5, 2);
            $table->timestamps();

            $table->index(['test_id', 'score_range_min', 'score_range_max']);
        });

        // Test bookmarks - for reviewing questions after test
        Schema::create('test_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'test_attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_bookmarks');
        Schema::dropIfExists('test_analytics');
        Schema::dropIfExists('test_results');
        Schema::dropIfExists('test_attempt_answers');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('tests');
    }
};