<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\TestController;

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::resource('users', UserController::class)->names('admin.users');
        Route::resource('subjects', SubjectController::class)->names('admin.subjects');
        Route::resource('topics', TopicController::class)->names('admin.topics');
        Route::resource('chapters', ChapterController::class)->names('admin.chapters');
        Route::resource('questions', QuestionController::class)->names('admin.questions');
        Route::post('questions/import', [QuestionController::class, 'import'])->name('admin.questions.import');
        Route::get('questions/export', [QuestionController::class, 'export'])->name('admin.questions.export');

        Route::resource('tests', TestController::class)->names('admin.tests');

        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
        Route::get('payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::get('payments/{id}', [PaymentController::class, 'show'])->name('admin.payments.show');

        Route::resource('invoices', InvoiceController::class)->names('admin.invoices');
        Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('admin.invoices.download');

        Route::resource('coupons', CouponController::class)->names('admin.coupons');
        Route::resource('banners', BannerController::class)->names('admin.banners');

        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('activity-logs/{log}', [ActivityLogController::class, 'show'])->name('admin.activity-logs.show');
        Route::post('activity-logs/clear', [ActivityLogController::class, 'clearOldLogs'])->name('admin.activity-logs.clear');
    });
