<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
  public function index()
  {
    $lessons = Lesson::withCount(['words', 'quizzes'])
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
    ], [
      'title.required' => 'Vui lòng nhập tiêu đề bài học.',
      'description.required' => 'Vui lòng nhập mô tả bài học.',
      'level.required' => 'Vui lòng chọn cấp độ.',
      'level.in' => 'Cấp độ không hợp lệ.',
      'duration.required' => 'Vui lòng nhập thời gian.',
      'duration.min' => 'Thời gian phải lớn hơn 0.',
      'order.required' => 'Vui lòng nhập thứ tự.',
      'order.min' => 'Thứ tự phải lớn hơn 0.',
      'content.required' => 'Vui lòng nhập nội dung bài học.',
      'content.json' => 'Nội dung phải là JSON hợp lệ.',
    ]);

    // Kiểm tra cấu trúc JSON
    $contentRaw = $request->input('content');
    $contentArray = json_decode($contentRaw, true);

    if (!isset($contentArray['sections']) || !is_array($contentArray['sections'])) {
      return back()->withErrors(['content' => 'JSON phải có mảng "sections" hợp lệ.'])->withInput();
    }

    // Lưu bài học với content đã chuẩn hóa
    Lesson::create([
      'title' => $request->input('title'),
      'description' => $request->input('description'),
      'level' => $request->input('level'),
      'duration' => $request->input('duration'),
      'order' => $request->input('order'),
      'content' => json_encode($contentArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ]);

    return redirect()->route('admin.lessons.index')
      ->with('success', 'Bài học đã được tạo thành công!');
  }


  public function show(Lesson $lesson)
  {
    $lesson->load(['words', 'quizzes', 'progress.user']);
    return view('admin.lessons.show', compact('lesson'));
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
    ], [
      'title.required' => 'Vui lòng nhập tiêu đề bài học.',
      'description.required' => 'Vui lòng nhập mô tả bài học.',
      'level.required' => 'Vui lòng chọn cấp độ.',
      'level.in' => 'Cấp độ không hợp lệ.',
      'duration.required' => 'Vui lòng nhập thời gian.',
      'duration.min' => 'Thời gian phải lớn hơn 0.',
      'order.required' => 'Vui lòng nhập thứ tự.',
      'order.min' => 'Thứ tự phải lớn hơn 0.',
      'content.required' => 'Vui lòng nhập nội dung bài học.',
      'content.json' => 'Nội dung phải là JSON hợp lệ.',
    ]);

    // Kiểm tra cấu trúc JSON
    $contentRaw = $request->input('content');
    $contentArray = json_decode($contentRaw, true);

    if (!isset($contentArray['sections']) || !is_array($contentArray['sections'])) {
      return back()->withErrors(['content' => 'JSON phải có mảng "sections" hợp lệ.'])->withInput();
    }

    // Cập nhật dữ liệu bài học
    $lesson->update([
      'title' => $request->input('title'),
      'description' => $request->input('description'),
      'level' => $request->input('level'),
      'duration' => $request->input('duration'),
      'order' => $request->input('order'),
      'content' => json_encode($contentArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ]);

    return redirect()->route('admin.lessons.index')
      ->with('success', 'Bài học đã được cập nhật thành công!');
  }


  public function destroy(Lesson $lesson)
  {
    // Check if lesson has related data
    $wordsCount = $lesson->words()->count();
    $quizzesCount = $lesson->quizzes()->count();
    $progressCount = $lesson->progress()->count();

    if ($wordsCount > 0 || $quizzesCount > 0 || $progressCount > 0) {
      return redirect()->route('admin.lessons.index')
        ->with('error', "Không thể xóa bài học này vì có {$wordsCount} từ vựng, {$quizzesCount} quiz và {$progressCount} tiến độ học tập liên quan.");
    }

    $lesson->delete();

    return redirect()->route('admin.lessons.index')
      ->with('success', 'Bài học đã được xóa thành công!');
  }
}
