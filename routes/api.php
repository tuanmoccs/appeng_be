<?php

use App\Http\Controllers\Client\QuizController;
use App\Http\Controllers\Client\WordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->group(function () {
    // Word routes
    Route::get('/words', [WordController::class, 'index']);
    Route::get('/words/{id}', [WordController::class, 'show']);
    Route::post('/words', [WordController::class, 'store']);
    Route::put('/words/{id}', [WordController::class, 'update']);
    Route::delete('/words/{id}', [WordController::class, 'destroy']);

    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::get('/quizzes/{id}', [QuizController::class, 'show']);
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit']);
    Route::get('/lessons/{lessonId}/quizzes', [QuizController::class, 'getByLesson']);
    Route::get('/user/quiz-results', [QuizController::class, 'getUserResults']);
});
