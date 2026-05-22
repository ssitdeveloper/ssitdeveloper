<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Web\QuestionBankController;

// Public routes - Frontend Marketing Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', function () { return view('features'); })->name('features');
Route::get('/pricing', function () { return view('pricing'); })->name('pricing');
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/contact', function () { return view('contact'); })->name('contact');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/student/dashboard', [DashboardController::class, 'index'])->name('student.dashboard');

    // Question Bank Routes
    Route::prefix('question-bank')->name('question-bank.')->group(function () {
        Route::get('/', [QuestionBankController::class, 'index'])->name('index');
        Route::get('/chapter/{chapterId}', [QuestionBankController::class, 'chapter'])->name('chapter');
        Route::get('/question/{questionId}', [QuestionBankController::class, 'show'])->name('show');
        Route::get('/practice', [QuestionBankController::class, 'practice'])->name('practice');
        Route::get('/search', [QuestionBankController::class, 'search'])->name('search');
        Route::get('/bookmarks', [QuestionBankController::class, 'bookmarks'])->name('bookmarks');

        // AJAX routes for filters
        Route::get('/api/subjects', [QuestionBankController::class, 'getSubjects'])->name('api.subjects');
        Route::get('/api/topics/{subjectId}', [QuestionBankController::class, 'getTopics'])->name('api.topics');
        Route::get('/api/chapters/{topicId}', [QuestionBankController::class, 'getChapters'])->name('api.chapters');

        // Actions
        Route::post('/question/{questionId}/validate', [QuestionBankController::class, 'validateAnswer'])->name('validate-answer');
        Route::post('/question/{questionId}/bookmark', [QuestionBankController::class, 'toggleBookmark'])->name('toggle-bookmark');
    });

    Route::get('/subscriptions/plans', [SubscriptionController::class, 'showPlans'])->name('student.subscription.plans');
    Route::post('/subscriptions/create', [SubscriptionController::class, 'create'])->name('student.subscription.create');
    Route::post('/subscriptions/verify', [SubscriptionController::class, 'verify'])->name('student.subscription.verify');
    Route::get('/payments/history', [SubscriptionController::class, 'paymentHistory'])->name('student.payment.history');
});

// Include mock test web routes
require __DIR__.'/web_mock_tests.php';

// Admin Authentication Routes
Route::get('/admin/login', function () {
    return view('auth.admin_login');
})->name('admin.login');

Route::post('/admin/login', [\App\Http\Controllers\AuthController::class, 'adminLogin'])
    ->name('admin.login.store')
    ->middleware('throttle:50,15'); // 50 attempts per 15 minutes for development

// Admin Routes - Protected with admin middleware and audit logging
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'audit'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/tests', [\App\Http\Controllers\Admin\TestController::class, 'index'])->name('tests.index');

    // Subjects Management
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);

    // Questions Management
    Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class);

    // Tests Management
    Route::resource('tests', \App\Http\Controllers\Admin\TestController::class);
});

// Logout route
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer Portal Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');

    Route::get('/my-courses', function () {
        return view('student.my_courses');
    })->name('my_courses');

    Route::get('/my-tests', function () {
        return view('student.my_tests');
    })->name('my_tests');

    Route::get('/test-history', function () {
        return view('student.test_history');
    })->name('test_history');

    Route::get('/bookmarks', function () {
        return view('student.bookmarks');
    })->name('bookmarks');

    Route::get('/performance', function () {
        return view('student.performance');
    })->name('performance');

    Route::get('/subscription', function () {
        return view('student.subscription');
    })->name('subscription');

    Route::get('/settings', function () {
        return view('student.settings');
    })->name('settings');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'studentLogin'])
    ->name('login.store')
    ->middleware('throttle:50,15'); // 50 attempts per 15 minutes for development

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::fallback(function () {
    return abort(404);
});
