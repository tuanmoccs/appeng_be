<?php

use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\QuizController;
use App\Http\Controllers\Client\WordController;
use App\Http\Controllers\Client\LessonController;
use App\Http\Controllers\Client\TestController;
use App\Http\Controllers\Client\ListeningTestController;
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
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/send-reset-otp', [AuthController::class, 'sendResetOTP']);
    Route::post('/reset-password-otp', [AuthController::class, 'resetPasswordWithOTP']);
});

// Public routes (không cần authentication)
Route::get('/words', [WordController::class, 'index']);
Route::get('/words/{id}', [WordController::class, 'show']);

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
    Route::get('/lessons', [LessonController::class, 'index']);
    Route::get('/lessons/stats', [LessonController::class, 'getStats']);
    Route::get('/lessons/{id}', [LessonController::class, 'show']);
    Route::post('/lessons/{id}/progress', [LessonController::class, 'updateProgress']);
    Route::post('/lessons/{id}/complete', [LessonController::class, 'complete']);
    // Quiz routes
    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::get('/quizzes/{id}', [QuizController::class, 'show']);
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
    Route::get('/user/quiz-results', [QuizController::class, 'getUserResults']);
    //Test routes
    Route::get('/tests', [TestController::class, 'index']);
    Route::get('/tests/{id}', [TestController::class, 'show']);
    Route::post('/tests/{id}/submit', [TestController::class, 'submitTest']);
    Route::get('/tests/{id}/results', [TestController::class, 'getUserResults']);

    Route::get('/listening-tests', [ListeningTestController::class, 'index']);

    // Get specific listening test with sections and questions
    Route::get('/listening-tests/{id}', [ListeningTestController::class, 'show']);

    // Submit listening test answers
    Route::post('/listening-tests/{id}/submit', [ListeningTestController::class, 'submit']);

    // Get user listening test results
    Route::get('/listening-tests/{id}/results', [ListeningTestController::class, 'results']);
});
