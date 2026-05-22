<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\QuestionController;
use App\Http\Controllers\Student\TestController;
use App\Http\Controllers\Student\AnalyticsController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\SubscriptionController;
use App\Http\Controllers\Student\BookmarkController;
use App\Http\Controllers\Student\SettingsController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\TestHistoryController;

Route::prefix('student')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('student.dashboard');

        // Practice mode
        Route::get('practice', [QuestionController::class, 'practice'])->name('student.practice');
        Route::get('practice/{chapterId}', [QuestionController::class, 'practiceByChapter'])->name('student.practice.chapter');
        Route::post('practice/answer', [QuestionController::class, 'submitAnswer'])->name('student.practice.answer');

        // Test mode with explicit model binding
        Route::get('tests', [TestController::class, 'index'])->name('student.tests');
        Route::get('tests/{slug}', [TestController::class, 'show'])->name('student.tests.show');
        Route::post('tests/{slug}/start', [TestController::class, 'start'])->name('student.tests.start');
        Route::get('tests/{slug}/attempt/{attemptId}', [TestController::class, 'attempt'])->name('student.tests.attempt');
        Route::post('tests/{slug}/submit-answer', [TestController::class, 'submitAnswer'])->name('student.tests.submit-answer');
        Route::get('tests/{slug}/result/{attemptId}', [TestController::class, 'result'])->name('student.tests.result');

        // Analytics
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('student.analytics');
        Route::get('analytics/weak-topics', [AnalyticsController::class, 'weakTopics'])->name('student.analytics.weak-topics');
        Route::get('analytics/progress', [AnalyticsController::class, 'progress'])->name('student.analytics.progress');
        Route::get('analytics/test-history', [AnalyticsController::class, 'testHistory'])->name('student.analytics.test-history');
        Route::get('leaderboard', [AnalyticsController::class, 'leaderboard'])->name('student.leaderboard');

        // Bookmarks
        Route::get('bookmarks', [BookmarkController::class, 'index'])->name('student.bookmarks');
        Route::post('bookmarks', [BookmarkController::class, 'store'])->name('student.bookmarks.store');
        Route::delete('bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('student.bookmarks.destroy');

        // Profile
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('student.profile.edit');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('student.profile.update');

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('student.settings');
        Route::post('settings/notifications', [SettingsController::class, 'updateNotifications'])->name('student.settings.notifications');
        Route::post('settings/preferences', [SettingsController::class, 'updatePreferences'])->name('student.settings.preferences');
        Route::post('settings/password', [SettingsController::class, 'changePassword'])->name('student.settings.password');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('student.notifications');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('student.notifications.read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('student.notifications.mark-all-read');
        Route::delete('notifications/{notification}', [NotificationController::class, 'delete'])->name('student.notifications.delete');

        // Test History
        Route::get('test-history', [TestHistoryController::class, 'index'])->name('student.test-history');
        Route::get('test-history/{attempt}', [TestHistoryController::class, 'show'])->name('student.test-history.show');

        // Subscription
        Route::get('subscription', [SubscriptionController::class, 'show'])->name('student.subscription');
        Route::get('subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('student.subscription.upgrade');
        Route::post('subscription/upgrade', [SubscriptionController::class, 'processUpgrade'])->name('student.subscription.upgrade.process');
        Route::get('subscription/stripe-callback', [SubscriptionController::class, 'stripeCallback'])->name('student.subscription.stripe-callback');
        Route::get('subscription/paypal-callback', [SubscriptionController::class, 'paypalCallback'])->name('student.subscription.paypal-callback');
        Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('student.subscription.cancel');
    });
