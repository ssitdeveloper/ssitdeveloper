<?php

use App\Http\Controllers\Api\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mock Test API Routes
|--------------------------------------------------------------------------
|
| These routes are for the mock test API endpoints. They are prefixed with
| 'api/mock-tests' and require authentication.
|
*/

Route::middleware(['auth:sanctum'])->prefix('mock-tests')->group(function () {

    // Test management
    Route::get('/', [TestController::class, 'index']);
    Route::get('/{test}', [TestController::class, 'show']);
    Route::post('/{test}/start', [TestController::class, 'startTest']);
    Route::get('/{test}/leaderboard', [TestController::class, 'getLeaderboard']);

    // Test attempt management
    Route::get('/attempts/{attempt}', [TestController::class, 'getAttempt']);
    Route::get('/attempts/{attempt}/question', [TestController::class, 'getQuestion']);
    Route::post('/attempts/{attempt}/answer', [TestController::class, 'saveAnswer']);
    Route::patch('/attempts/{attempt}/time', [TestController::class, 'updateTime']);
    Route::post('/attempts/{attempt}/submit', [TestController::class, 'submitTest']);
    Route::get('/attempts/{attempt}/result', [TestController::class, 'getResult']);
    Route::post('/attempts/{attempt}/resume', [TestController::class, 'resumeTest']);
    Route::post('/attempts/{attempt}/bookmark', [TestController::class, 'bookmarkQuestion']);

    // User-specific routes
    Route::get('/user/attempts', [TestController::class, 'getUserAttempts']);
    Route::get('/user/statistics', [TestController::class, 'getUserStatistics']);

});