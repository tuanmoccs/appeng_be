<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
  public function index()
  {
    $lessons = Lesson::withCount(['quizResults'])
      ->orderBy('order')
      ->paginate(10);
    return view('admin.lessons.index', compact('lessons'));
  }

  public function create()
  {
    return view('admin.lessons.create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'required|string',
      'level' => 'required|in:beginner,intermediate,advanced',
      'duration' => 'required|integer|min:1',
      'order' => 'required|integer|min:1',
      'content' => 'required|json',
      'quiz' => 'nullable|json',
    ], [
      'title.required' => 'Vui lòng nhập tiêu đề bài học.',
      'description.required' => 'Vui lòng nhập mô tả bài học.',
      'level.required' => 'Vui lòng chọn cấp độ.',
      'duration.required' => 'Vui lòng nhập thời gian.',
      'order.required' => 'Vui lòng nhập thứ tự.',
      'content.required' => 'Vui lòng nhập nội dung bài học.',
      'content.json' => 'Nội dung phải là JSON hợp lệ.',
      'quiz.json' => 'Quiz phải là JSON hợp lệ.',
    ]);

    // Validate content structure
    $contentArray = json_decode($request->input('content'), true);
    if (!isset($contentArray['sections']) || !is_array($contentArray['sections'])) {
      return back()->withErrors(['content' => 'JSON phải có mảng "sections" hợp lệ.'])->withInput();
    }

    // Validate quiz structure if provided
    $quizArray = null;
    if ($request->filled('quiz')) {
      $quizArray = json_decode($request->input('quiz'), true);
      if (!isset($quizArray['questions']) || !is_array($quizArray['questions'])) {
        return back()->withErrors(['quiz' => 'Quiz phải có mảng "questions" hợp lệ.'])->withInput();
      }
    }

    Lesson::create([
      'title' => $request->input('title'),
      'description' => $request->input('description'),
      'level' => $request->input('level'),
      'duration' => $request->input('duration'),
      'order' => $request->input('order'),
      'content' => $contentArray,
      'quiz' => $quizArray,
    ]);

    return redirect()->route('admin.lessons.index')
      ->with('success', 'Bài học đã được tạo thành công!');
  }


  public function show(Lesson $lesson)
  {
    $lesson->load(['words', 'progress.user', 'quizResults']);

    // Get quiz statistics
    $quizStats = [
      'total_attempts' => $lesson->quizResults()->count(),
      'passed_attempts' => $lesson->quizResults()->where('is_passed', true)->count(),
      'average_score' => $lesson->quizResults()->avg('score'),
    ];

    return view('admin.lessons.show', compact('lesson', 'quizStats'));
  }

  public function edit(Lesson $lesson)
  {
    return view('admin.lessons.edit', compact('lesson'));
  }

  public function update(Request $request, Lesson $lesson)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'required|string',
      'level' => 'required|in:beginner,intermediate,advanced',
      'duration' => 'required|integer|min:1',
      'order' => 'required|integer|min:1',
      'content' => 'required|json',
      'quiz' => 'nullable|json',
    ]);

    $contentArray = json_decode($request->input('content'), true);
    if (!isset($contentArray['sections']) || !is_array($contentArray['sections'])) {
      return back()->withErrors(['content' => 'JSON phải có mảng "sections" hợp lệ.'])->withInput();
    }

    $quizArray = null;
    if ($request->filled('quiz')) {
      $quizArray = json_decode($request->input('quiz'), true);
      if (!isset($quizArray['questions']) || !is_array($quizArray['questions'])) {
        return back()->withErrors(['quiz' => 'Quiz phải có mảng "questions" hợp lệ.'])->withInput();
      }
    }

    $lesson->update([
      'title' => $request->input('title'),
      'description' => $request->input('description'),
      'level' => $request->input('level'),
      'duration' => $request->input('duration'),
      'order' => $request->input('order'),
      'content' => $contentArray,
      'quiz' => $quizArray,
    ]);

    return redirect()->route('admin.lessons.index')
      ->with('success', 'Bài học đã được cập nhật thành công!');
  }


  public function destroy(Lesson $lesson)
  {
    $wordsCount = $lesson->words()->count();
    //$quizzesCount = $lesson->quizzes()->count();
    $progressCount = $lesson->progress()->count();
    $quizResultsCount = $lesson->quizResults()->count();

    if ($wordsCount > 0 || $progressCount > 0 || $quizResultsCount > 0) {
      return redirect()->route('admin.lessons.index')
        ->with('error', "Không thể xóa bài học này vì có dữ liệu liên quan.");
    }

    $lesson->delete();

    return redirect()->route('admin.lessons.index')
      ->with('success', 'Bài học đã được xóa thành công!');
  }
}
