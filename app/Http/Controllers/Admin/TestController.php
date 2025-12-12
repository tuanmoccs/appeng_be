<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Http\Request;

class TestController extends Controller
{
  public function index()
  {
    $tests = Test::withCount(['questions', 'passages'])->paginate(10);
    return view('admin.tests.index', compact('tests'));
  }

  public function create()
  {
    return view('admin.tests.create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'type' => 'required|in:placement,achievement,practice',
      'total_questions' => 'required|integer|min:10|max:200',
      'time_limit' => 'nullable|integer|min:5',
      'passing_score' => 'required|integer|min:0|max:100',
    ]);

    Test::create($request->all());

    return redirect()->route('admin.tests.index')
      ->with('success', 'Bài test đã được tạo thành công!');
  }

  public function show(Test $test)
  {
    $test->load(['questions', 'passages.questions', 'results.user']);
    return view('admin.tests.show', compact('test'));
  }

  public function edit(Test $test)
  {
    return view('admin.tests.edit', compact('test'));
  }

  public function update(Request $request, Test $test)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'type' => 'required|in:placement,achievement,practice',
      'total_questions' => 'required|integer|min:10|max:200',
      'time_limit' => 'nullable|integer|min:5',
      'passing_score' => 'required|integer|min:0|max:100',
    ]);

    $test->update($request->all());

    return redirect()->route('admin.tests.index')
      ->with('success', 'Bài test đã được cập nhật thành công!');
  }

  public function destroy(Test $test)
  {
    $test->delete();

    return redirect()->route('admin.tests.index')
      ->with('success', 'Bài test đã được xóa thành công!');
  }

  // Quản lý questions (standalone)
  public function questions(Test $test)
  {
    $standaloneQuestions = $test->standaloneQuestions()->paginate(20);
    return view('admin.tests.questions', compact('test', 'standaloneQuestions'));
  }

  public function createQuestion(Test $test)
  {
    $nextOrder = $test->standaloneQuestions()->max('order') + 1;
    return view('admin.tests.create-question', compact('test', 'nextOrder'));
  }

  public function storeQuestion(Request $request, Test $test)
  {
    $request->validate([
      'question' => 'required|string',
      'options' => 'required|array|min:2|max:6',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
      'difficulty' => 'required|in:easy,medium,hard',
      'order' => 'required|integer|min:1',
    ]);

    $test->questions()->create([
      'type' => 'standalone',
      'passage_id' => null,
      'question' => $request->question,
      'options' => $request->options,
      'correct_answer' => $request->correct_answer,
      'difficulty' => $request->difficulty,
      'order' => $request->order,
    ]);

    return redirect()->route('admin.tests.questions', $test)
      ->with('success', 'Câu hỏi đã được thêm thành công!');
  }

  public function editQuestion(Test $test, TestQuestion $question)
  {
    return view('admin.tests.edit-question', compact('test', 'question'));
  }

  public function updateQuestion(Request $request, Test $test, TestQuestion $question)
  {
    $request->validate([
      'question' => 'required|string',
      'options' => 'required|array|min:2|max:6',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
      'difficulty' => 'required|in:easy,medium,hard',
      'order' => 'required|integer|min:1',
    ]);

    $question->update($request->all());

    return redirect()->route('admin.tests.questions', $test)
      ->with('success', 'Câu hỏi đã được cập nhật thành công!');
  }

  public function destroyQuestion(Test $test, TestQuestion $question)
  {
    $question->delete();

    return redirect()->route('admin.tests.questions', $test)
      ->with('success', 'Câu hỏi đã được xóa thành công!');
  }
}
