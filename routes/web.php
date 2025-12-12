<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WordController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\ListeningTestController;
use App\Http\Controllers\Admin\ListeningSectionController;
use App\Http\Controllers\Admin\ListeningQuestionController;
use App\Http\Controllers\Admin\PassageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index']);

        // Users management
        Route::resource('users', UserController::class);

        // Lessons management
        Route::resource('lessons', LessonController::class);
        Route::post('lessons', [LessonController::class, 'store'])->name('lessons.store');

        // Words management
        Route::resource('words', WordController::class);
        Route::get('words/import/form', [WordController::class, 'importForm'])->name('words.import');
        Route::post('words/import/form', [WordController::class, 'import'])->name('words.import.process');

        // Quizzes management
        Route::resource('quizzes', QuizController::class);
        Route::get('quizzes/{quiz}/questions', [QuizController::class, 'questions'])->name('quizzes.questions');
        Route::get('quizzes/{quiz}/questions/create', [QuizController::class, 'createQuestion'])->name('quizzes.questions.create');
        Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::get('quizzes/{quiz}/questions/{question}/edit', [QuizController::class, 'editQuestion'])->name('quizzes.questions.edit');
        Route::put('quizzes/{quiz}/questions/{question}', [QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
        Route::delete('quizzes/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');

        // Tests management
        Route::resource('tests', TestController::class);
        Route::prefix('tests/{test}')->name('tests.')->group(function () {
            Route::get('questions', [TestController::class, 'questions'])->name('questions');
            Route::get('questions/create', [TestController::class, 'createQuestion'])->name('questions.create');
            Route::post('questions', [TestController::class, 'storeQuestion'])->name('questions.store');
            Route::get('questions/{question}/edit', [TestController::class, 'editQuestion'])->name('questions.edit');
            Route::put('questions/{question}', [TestController::class, 'updateQuestion'])->name('questions.update');
            Route::delete('questions/{question}', [TestController::class, 'destroyQuestion'])->name('questions.destroy');

            // Passages Management
            Route::get('passages', [PassageController::class, 'index'])->name('passages.index');
            Route::get('passages/create', [PassageController::class, 'create'])->name('passages.create');
            Route::post('passages', [PassageController::class, 'store'])->name('passages.store');
            Route::get('passages/{passage}', [PassageController::class, 'show'])->name('passages.show');
            Route::get('passages/{passage}/edit', [PassageController::class, 'edit'])->name('passages.edit');
            Route::put('passages/{passage}', [PassageController::class, 'update'])->name('passages.update');
            Route::delete('passages/{passage}', [PassageController::class, 'destroy'])->name('passages.destroy');

            // Passage Questions
            Route::get('passages/{passage}/questions/create', [PassageController::class, 'createQuestion'])
                ->name('passages.create-question');
            Route::post('passages/{passage}/questions', [PassageController::class, 'storeQuestion'])
                ->name('passages.store-question');
        });
        // Listening Tests Management
        Route::resource('listening-tests', ListeningTestController::class);
        Route::get('listening-tests/{test}/sections', [ListeningTestController::class, 'sections'])->name('listening-tests.sections');
        Route::patch('listening-tests/{test}/toggle-status', [ListeningTestController::class, 'toggleStatus'])->name('listening-tests.toggle-status');

        // Listening Sections Management
        Route::prefix('listening-tests/{test}/sections')->group(function () {
            Route::post('/', [ListeningSectionController::class, 'store'])->name('listening-sections.store');
            Route::put('/{section}', [ListeningSectionController::class, 'update'])->name('listening-sections.update');
            Route::delete('/{section}', [ListeningSectionController::class, 'destroy'])->name('listening-sections.destroy');
        });

        Route::prefix('listening-sections')->group(function () {
            Route::post('{section}/questions', [ListeningQuestionController::class, 'store'])->name('listening-questions.store');
            Route::put('{section}/questions/{question}', [ListeningQuestionController::class, 'update'])->name('listening-questions.update');
            Route::delete('{section}/questions/{question}', [ListeningQuestionController::class, 'destroy'])->name('listening-questions.destroy');
        });
        // Listening Questions Management
        //     Route::post('listening-sections/{section}/questions', [ListeningQuestionController::class, 'store'])->name('listening-questions.store');
        //     Route::put('listening-sections/{section}/questions/{question}', [ListeningQuestionController::class, 'update'])->name('listening-questions.update');
        //     Route::delete('listening-sections/{section}/questions/{question}', [ListeningQuestionController::class, 'destroy'])->name('listening-questions.destroy');
    });
});
