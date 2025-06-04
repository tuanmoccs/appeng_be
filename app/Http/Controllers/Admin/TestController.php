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
    $tests = Test::withCount('questions')->paginate(10);
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
    $test->load(['questions', 'results.user']);
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

  public function questions(Test $test)
  {
    $questions = $test->questions()->orderBy('order')->paginate(20);
    return view('admin.tests.questions', compact('test', 'questions'));
  }

  public function createQuestion(Test $test)
  {
    return view('admin.tests.create-question', compact('test'));
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

    $test->questions()->create($request->all());

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
