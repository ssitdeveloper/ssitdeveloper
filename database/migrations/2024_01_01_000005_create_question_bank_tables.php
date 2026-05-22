<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subjects table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->integer('order_by')->default(0);
            $table->timestamps();

            $table->index(['order_by']);
        });

        // Topics table
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order_by')->default(0);
            $table->timestamps();

            $table->unique(['subject_id', 'name']);
            $table->index(['subject_id']);
            $table->index(['order_by']);
        });

        // Chapters table
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order_by')->default(0);
            $table->timestamps();

            $table->unique(['topic_id', 'name']);
            $table->index(['topic_id']);
            $table->index(['order_by']);
        });

        // Questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->longText('question_text');
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('type', ['single_choice', 'multiple_choice'])->default('single_choice');
            $table->json('tags')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->longText('explanation')->nullable();
            $table->string('solution_video_url', 500)->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('attempts_count')->default(0);
            $table->timestamps();

            $table->index(['chapter_id', 'difficulty_level']);
            $table->index(['is_published', 'difficulty_level']);
            $table->fullText(['question_text']);
        });

        // Options table
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->longText('option_text');
            $table->boolean('is_correct')->default(false);
            $table->text('explanation')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->integer('order_by')->default(0);
            $table->timestamps();

            $table->index(['question_id']);
            $table->index(['is_correct']);
            $table->index(['question_id', 'is_correct']);
        });

        // Bookmarks table
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
            $table->index(['user_id']);
            $table->index(['question_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('subjects');
    }
};
