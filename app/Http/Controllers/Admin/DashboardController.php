<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Word;
use App\Models\Test;
use App\Models\UserLessonProgress;
use App\Models\UserQuizResult;
use App\Models\UserTestResult;

class DashboardController extends Controller
{
  public function index()
  {
    $stats = [
      'total_users' => User::count(),
      'total_lessons' => Lesson::count(),
      'total_quizzes' => Quiz::count(),
      'total_words' => Word::count(),
      'total_tests' => Test::count(),
      'completed_lessons' => UserLessonProgress::where('is_completed', true)->count(),
      'quiz_attempts' => UserQuizResult::count(),
      'test_attempts' => UserTestResult::count(),
    ];

    $recent_users = User::latest()->take(5)->get();
    $recent_quiz_results = UserQuizResult::with(['user', 'quiz'])
      ->latest()
      ->take(5)
      ->get();

    return view('admin.dashboard', compact('stats', 'recent_users', 'recent_quiz_results'));
  }
}
