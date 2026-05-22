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
        // Learning sessions table - tracks user's learning progress
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->enum('mode', ['subject', 'topic', 'chapter', 'custom'])->default('subject');
            $table->integer('total_questions')->default(0);
            $table->integer('questions_completed')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('current_question_index')->default(0);
            $table->json('session_data')->nullable(); // Store session metadata
            $table->enum('status', ['active', 'paused', 'completed', 'abandoned'])->default('active');
            $table->datetime('started_at')->nullable();
            $table->datetime('paused_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('subject_id');
            $table->index('created_at');
        });

        // Learning question attempts - tracks individual question attempts
        Schema::create('learning_question_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('options')->nullOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('attempt_number')->default(1);
            $table->text('user_explanation')->nullable(); // User can add their reasoning
            $table->boolean('is_bookmarked')->default(false);
            $table->boolean('is_reviewed')->default(false);
            $table->enum('difficulty_rating', ['easy', 'medium', 'hard', 'unrated'])->default('unrated');
            $table->json('metadata')->nullable(); // Additional data like hints used
            $table->timestamps();

            $table->index(['learning_session_id', 'question_id']);
            $table->index(['user_id', 'question_id']);
            $table->unique(['learning_session_id', 'question_id'], 'lqa_session_question_unique');
        });

        // Learning explanations - custom explanations for questions
        Schema::create('learning_explanations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->longText('detailed_explanation');
            $table->text('key_concepts')->nullable(); // Key learning points
            $table->text('similar_questions')->nullable(); // JSON array of related question IDs
            $table->integer('views_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('unhelpful_count')->default(0);
            $table->enum('type', ['text', 'video', 'image', 'mixed'])->default('text');
            $table->string('video_url')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->timestamps();

            $table->index('question_id');
        });

        // User learning preferences and settings
        Schema::create('user_learning_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('show_explanation_after_answer')->default(true);
            $table->enum('explanation_language', ['english', 'hindi'])->default('english');
            $table->integer('questions_per_session')->default(10);
            $table->boolean('enable_adaptive_mode')->default(true);
            $table->boolean('show_hints')->default(true);
            $table->integer('hint_limit_per_question')->default(2);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('show_difficulty_indicator')->default(true);
            $table->json('preferred_learning_style')->nullable(); // visual, auditory, kinesthetic
            $table->json('notification_preferences')->nullable();
            $table->timestamps();
        });

        // Weak topic detection and tracking
        Schema::create('user_weak_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->integer('total_attempts')->default(0);
            $table->integer('correct_attempts')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->integer('difficulty_score')->default(0); // 0-100, higher = more difficult for user
            $table->enum('status', ['weak', 'average', 'good', 'excellent'])->default('weak');
            $table->datetime('last_attempted_at')->nullable();
            $table->datetime('last_improved_at')->nullable();
            $table->json('trend_data')->nullable(); // Historical accuracy data
            $table->json('recommendations')->nullable(); // Personalized learning recommendations
            $table->timestamps();

            $table->unique(['user_id', 'topic_id']);
            $table->index(['user_id', 'status']);
        });

        // Learning recommendations - AI-driven adaptive learning
        Schema::create('learning_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();
            $table->enum('recommendation_type', ['weak_topic', 'similar_topics', 'next_topic', 'revision', 'advanced'])->default('weak_topic');
            $table->text('recommendation_text');
            $table->integer('priority')->default(0); // 0-100, higher = more important
            $table->integer('estimated_time_minutes')->default(30);
            $table->json('target_question_ids')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_accepted')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['recommendation_type', 'is_active']);
        });

        // Learning progress tracking
        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('total_questions_attempted')->default(0);
            $table->integer('total_correct')->default(0);
            $table->decimal('overall_accuracy', 5, 2)->default(0);
            $table->integer('total_time_minutes')->default(0);
            $table->integer('streak_days')->default(0);
            $table->datetime('last_activity_at')->nullable();
            $table->json('weekly_stats')->nullable(); // Stats for last 7 days
            $table->json('monthly_stats')->nullable(); // Stats for last 30 days
            $table->json('chapter_progress')->nullable(); // Chapter-wise progress
            $table->timestamps();

            $table->unique(['user_id', 'subject_id']);
            $table->index(['user_id', 'subject_id']);
        });

        // Session question history - for resume functionality
        Schema::create('session_question_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->integer('question_order')->default(0);
            $table->enum('visit_status', ['not_visited', 'visited', 'answered', 'reviewed'])->default('not_visited');
            $table->timestamps();

            $table->unique(['learning_session_id', 'question_id']);
        });

        // User hints used
        Schema::create('learning_hints_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->integer('hint_number')->default(1);
            $table->text('hint_text');
            $table->integer('time_to_hint_seconds')->default(0);
            $table->timestamps();

            $table->index(['learning_session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_hints_used');
        Schema::dropIfExists('session_question_history');
        Schema::dropIfExists('learning_progress');
        Schema::dropIfExists('learning_recommendations');
        Schema::dropIfExists('user_weak_topics');
        Schema::dropIfExists('user_learning_preferences');
        Schema::dropIfExists('learning_explanations');
        Schema::dropIfExists('learning_question_attempts');
        Schema::dropIfExists('learning_sessions');
    }
};
