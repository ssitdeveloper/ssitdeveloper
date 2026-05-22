<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin users table
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('role')->default('admin'); // admin, editor, moderator
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('last_login_at')->nullable();
            $table->timestamps();

            $table->index('role');
        });

        // Banners for homepage and dashboard
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->enum('placement', ['homepage', 'dashboard', 'popup'])->default('homepage');
            $table->integer('order')->default(0);
            $table->datetime('starts_at')->nullable();
            $table->datetime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['placement', 'is_active']);
        });

        // Subscription plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('INR');
            $table->integer('duration_days');
            $table->json('features')->nullable();
            $table->integer('max_tests')->nullable();
            $table->integer('max_practice_questions')->nullable();
            $table->boolean('has_analytics')->default(false);
            $table->boolean('has_adaptive_learning')->default(false);
            $table->boolean('has_doubt_clearing')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // Payment transactions
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('INR');
            $table->string('payment_gateway'); // razorpay, stripe
            $table->string('gateway_transaction_id')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('gateway_transaction_id');
        });

        // Notifications
        Schema::create('notifications_config', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->text('template');
            $table->enum('type', ['email', 'sms', 'push', 'in_app'])->default('in_app');
            $table->json('variables')->nullable(); // Dynamic variables in template
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // User notifications sent
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('notifications_config_id')->constrained('notifications_config');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->datetime('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });

        // Analytics events
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_name');
            $table->string('event_category'); // user_signup, test_start, answer_submit, etc.
            $table->json('event_data')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['event_name', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // System settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type'); // string, boolean, json, number
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('notifications_config');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('admin_users');
    }
};
