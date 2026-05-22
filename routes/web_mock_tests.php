<?php

use App\Http\Controllers\Web\MockTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mock Test Web Routes
|--------------------------------------------------------------------------
|
| These routes are for the mock test web interface. They are prefixed with
| 'mock-tests' and require authentication.
|
*/

Route::middleware(['auth'])->prefix('mock-tests')->name('mock-tests.')->group(function () {

    // Test listing and details
    Route::get('/', [MockTestController::class, 'index'])->name('index');
    Route::get('/{test}', [MockTestController::class, 'show'])->name('show');
    Route::post('/{test}/start', [MockTestController::class, 'start'])->name('start');

    // Test taking interface
    Route::get('/attempt/{attempt}', [MockTestController::class, 'take'])->name('take');
    Route::get('/attempt/{attempt}/question', [MockTestController::class, 'getQuestion'])->name('question');
    Route::post('/attempt/{attempt}/answer', [MockTestController::class, 'saveAnswer'])->name('save-answer');
    Route::patch('/attempt/{attempt}/time', [MockTestController::class, 'updateTime'])->name('update-time');
    Route::post('/attempt/{attempt}/submit', [MockTestController::class, 'submit'])->name('submit');
    Route::post('/attempt/{attempt}/bookmark', [MockTestController::class, 'bookmark'])->name('bookmark');

    // Results and review
    Route::get('/attempt/{attempt}/result', [MockTestController::class, 'result'])->name('result');
    Route::get('/attempt/{attempt}/review', [MockTestController::class, 'review'])->name('review');
    Route::post('/attempt/{attempt}/resume', [MockTestController::class, 'resume'])->name('resume');

    // User history and statistics
    Route::get('/history', [MockTestController::class, 'history'])->name('history');

    // Leaderboards
    Route::get('/{test}/leaderboard', [MockTestController::class, 'leaderboard'])->name('leaderboard');

});