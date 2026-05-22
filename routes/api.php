<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\AnalyticsController;

// Auth routes with rate limiting - 5 attempts per 15 minutes
Route::prefix('auth')
    ->middleware('throttle:5,15')
    ->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });

// Protected API routes with higher rate limit and subscription check
Route::middleware(['auth:sanctum', 'check.subscription', 'throttle:100,60'])->group(function () {
    // Question Bank API Routes
    Route::prefix('questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::get('/random', [QuestionController::class, 'getRandom']);
        Route::get('/search', [QuestionController::class, 'search']);
        Route::get('/stats', [QuestionController::class, 'getQuestionsStats']);
        Route::get('/chapter/{chapterId}', [QuestionController::class, 'getByChapter']);
        Route::get('/{id}', [QuestionController::class, 'show']);
        Route::get('/{id}/stats', [QuestionController::class, 'getStats']);
        Route::post('/{id}/validate-answer', [QuestionController::class, 'validateAnswer']);
        Route::post('/{id}/bookmark', [QuestionController::class, 'bookmark']);
        Route::delete('/{id}/bookmark', [QuestionController::class, 'removeBookmark']);
    });

    Route::get('user/bookmarks', [QuestionController::class, 'getBookmarks']);

    Route::get('tests', [TestController::class, 'index']);
    Route::get('tests/{id}', [TestController::class, 'show']);
    Route::post('tests/{id}/start', [TestController::class, 'start']);
    Route::post('tests/{id}/submit-answer', [TestController::class, 'submitAnswer']);
    Route::get('tests/{id}/result', [TestController::class, 'result']);

    Route::get('user/analytics', [AnalyticsController::class, 'getUserAnalytics']);
    Route::get('user/leaderboard', [AnalyticsController::class, 'getLeaderboard']);
});

// Include mock test API routes
require __DIR__.'/api_mock_tests.php';
