<?php

use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\QuizController;
use App\Http\Controllers\Client\WordController;
use App\Http\Controllers\Client\LessonController;
use App\Http\Controllers\Client\TestController;
use App\Http\Controllers\Client\ListeningTestController;
use App\Http\Controllers\Client\TwoFactorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/send-reset-otp', [AuthController::class, 'sendResetOTP']);
    Route::post('/reset-password-otp', [AuthController::class, 'resetPasswordWithOTP']);

    // Route::get('/{provider}', [AuthController::class, 'redirectToProvider']);
    // Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
});

// Public routes (không cần authentication)
Route::get('/words', [WordController::class, 'index']);
Route::get('/words/{id}', [WordController::class, 'show']);
Route::get('/lessons', [LessonController::class, 'index']);
Route::get('/quizzes', [QuizController::class, 'index']);
Route::get('/lastest-quizzes', [QuizController::class, 'GetLastestQuiz']);
Route::get('/tests', [TestController::class, 'index']);
Route::get('/lastest-tests', [TestController::class, 'GetLastestTest']);
Route::get('/listening-tests', [ListeningTestController::class, 'index']);
// Protected routes (cần authentication)
Route::middleware('auth:api')->group(function () {
    // Auth user info
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::get('/auth/achievements', [AuthController::class, 'getUserAchievements']);
    Route::get('/auth/stats', [AuthController::class, 'getUserStats']);

    // Lesson routes

    Route::prefix('lessons')->group(function () {
        Route::get('/', [LessonController::class, 'index']);
        Route::get('/stats', [LessonController::class, 'getStats']);
        Route::get('/{id}', [LessonController::class, 'show']);
        Route::post('/{id}/progress', [LessonController::class, 'updateProgress']);

        // Quiz routes
        Route::get('/{id}/quiz', [LessonController::class, 'getQuiz']);
        Route::post('/{id}/quiz/submit', [LessonController::class, 'submitQuiz']);
    });
    // Quiz routes

    Route::get('/quizzes/{id}', [QuizController::class, 'show']);
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
    Route::get('/user/quiz-results', [QuizController::class, 'getUserResults']);
    //Test routes

    Route::prefix('tests')->group(function () {
        Route::get('/', [TestController::class, 'index']);
        Route::get('/{id}', [TestController::class, 'show']);
        Route::post('/{testId}/submit', [TestController::class, 'submitTest'])->middleware('auth:api');
        Route::get('/{testId}/results', [TestController::class, 'getUserResults'])->middleware('auth:api');
    });



    // Get specific listening test with sections and questions
    Route::get('/listening-tests/{id}', [ListeningTestController::class, 'show']);

    // Submit listening test answers
    Route::post('/listening-tests/{id}/submit', [ListeningTestController::class, 'submit']);

    // Get user listening test results
    Route::get('/listening-tests/{id}/results', [ListeningTestController::class, 'results']);

    Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm']);
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);
    Route::post('/2fa/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes']);
});
Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
