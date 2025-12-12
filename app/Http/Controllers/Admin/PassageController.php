<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestPassage;
use Illuminate\Http\Request;

class PassageController extends Controller
{
  public function index(Test $test)
  {
    $passages = $test->passages()
      ->withCount('questions')
      ->orderBy('order')
      ->paginate(10);

    return view('admin.tests.passages.index', compact('test', 'passages'));
  }

  public function create(Test $test)
  {
    $nextOrder = $test->passages()->max('order') + 1;
    return view('admin.tests.passages.create', compact('test', 'nextOrder'));
  }

  public function store(Request $request, Test $test)
  {
    $request->validate([
      'title' => 'nullable|string|max:255',
      'content' => 'required|string|min:50',
      'order' => 'required|integer|min:1',
    ]);

    $test->passages()->create($request->all());

    return redirect()->route('admin.tests.passages.index', $test)
      ->with('success', 'Bài đọc đã được tạo thành công!');
  }

  public function show(Test $test, TestPassage $passage)
  {
    $passage->load('questions');
    return view('admin.tests.passages.show', compact('test', 'passage'));
  }

  public function edit(Test $test, TestPassage $passage)
  {
    return view('admin.tests.passages.edit', compact('test', 'passage'));
  }

  public function update(Request $request, Test $test, TestPassage $passage)
  {
    $request->validate([
      'title' => 'nullable|string|max:255',
      'content' => 'required|string|min:50',
      'order' => 'required|integer|min:1',
    ]);

    $passage->update($request->all());

    return redirect()->route('admin.tests.passages.index', $test)
      ->with('success', 'Bài đọc đã được cập nhật thành công!');
  }

  public function destroy(Test $test, TestPassage $passage)
  {
    $passage->delete();

    return redirect()->route('admin.tests.passages.index', $test)
      ->with('success', 'Bài đọc đã được xóa thành công!');
  }

  // Thêm câu hỏi cho passage
  public function createQuestion(Test $test, TestPassage $passage)
  {
    $nextOrder = $passage->questions()->max('order') + 1;
    return view('admin.tests.passages.create-question', compact('test', 'passage', 'nextOrder'));
  }

  public function storeQuestion(Request $request, Test $test, TestPassage $passage)
  {
    $request->validate([
      'question' => 'required|string',
      'options' => 'required|array|min:2|max:6',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
      'difficulty' => 'required|in:easy,medium,hard',
      'order' => 'required|integer|min:1',
    ]);

    $passage->questions()->create([
      'test_id' => $test->id,
      'type' => 'passage',
      'question' => $request->question,
      'options' => $request->options,
      'correct_answer' => $request->correct_answer,
      'difficulty' => $request->difficulty,
      'order' => $request->order,
    ]);

    return redirect()->route('admin.tests.passages.show', [$test, $passage])
      ->with('success', 'Câu hỏi đã được thêm thành công!');
  }
}
