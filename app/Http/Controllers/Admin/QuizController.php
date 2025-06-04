<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Lesson;
use App\Models\UserQuizResult;
use Illuminate\Http\Request;

class QuizController extends Controller
{
  /**
   * Display a listing of the quizzes.
   */
  public function index(Request $request)
  {
    $query = Quiz::query();

    // Filter by lesson
    if ($request->has('lesson_id') && $request->lesson_id) {
      $query->where('lesson_id', $request->lesson_id);
    }

    $quizzes = $query->with('lesson')
      ->withCount('questions')
      ->paginate(10);

    $lessons = Lesson::orderBy('order')->get();

    return view('admin.quizzes.index', compact('quizzes', 'lessons'));
  }

  /**
   * Show the form for creating a new quiz.
   */
  public function create()
  {
    $lessons = Lesson::orderBy('order')->get();
    return view('admin.quizzes.create', compact('lessons'));
  }

  /**
   * Store a newly created quiz in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'lesson_id' => 'required|exists:lessons,id',
    ]);

    $quiz = Quiz::create($request->all());

    return redirect()->route('admin.quizzes.questions', $quiz)
      ->with('success', 'Quiz đã được tạo thành công! Bây giờ hãy thêm câu hỏi.');
  }

  /**
   * Display the specified quiz.
   */
  public function show(Quiz $quiz)
  {
    $quiz->load(['lesson', 'questions']);

    // Lấy thống kê kết quả
    $totalAttempts = UserQuizResult::where('quiz_id', $quiz->id)->count();
    $avgScore = UserQuizResult::where('quiz_id', $quiz->id)->avg('score');

    return view('admin.quizzes.show', compact('quiz', 'totalAttempts', 'avgScore'));
  }

  /**
   * Show the form for editing the specified quiz.
   */
  public function edit(Quiz $quiz)
  {
    $lessons = Lesson::orderBy('order')->get();
    return view('admin.quizzes.edit', compact('quiz', 'lessons'));
  }

  /**
   * Update the specified quiz in storage.
   */
  public function update(Request $request, Quiz $quiz)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'lesson_id' => 'required|exists:lessons,id',
    ]);

    $quiz->update($request->all());

    return redirect()->route('admin.quizzes.index')
      ->with('success', 'Quiz đã được cập nhật thành công!');
  }

  /**
   * Remove the specified quiz from storage.
   */
  public function destroy(Quiz $quiz)
  {
    $quiz->delete();

    return redirect()->route('admin.quizzes.index')
      ->with('success', 'Quiz đã được xóa thành công!');
  }

  /**
   * Display questions for a quiz.
   */
  public function questions(Quiz $quiz)
  {
    $questions = $quiz->questions()->paginate(15);
    return view('admin.quizzes.questions', compact('quiz', 'questions'));
  }

  /**
   * Show form to create a new question.
   */
  public function createQuestion(Quiz $quiz)
  {
    return view('admin.quizzes.create-question', compact('quiz'));
  }

  /**
   * Store a new question.
   */
  public function storeQuestion(Request $request, Quiz $quiz)
  {
    $request->validate([
      'question' => 'required|string',
      'options' => 'required|array|min:2',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
    ]);

    $quiz->questions()->create([
      'question' => $request->question,
      'options' => $request->options,
      'correct_answer' => $request->correct_answer,
    ]);

    return redirect()->route('admin.quizzes.questions', $quiz)
      ->with('success', 'Câu hỏi đã được thêm thành công!');
  }

  /**
   * Show form to edit a question.
   */
  public function editQuestion(Quiz $quiz, QuizQuestion $question)
  {
    return view('admin.quizzes.edit-question', compact('quiz', 'question'));
  }

  /**
   * Update a question.
   */
  public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
  {
    $request->validate([
      'question' => 'required|string',
      'options' => 'required|array|min:2',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
    ]);

    $question->update([
      'question' => $request->question,
      'options' => $request->options,
      'correct_answer' => $request->correct_answer,
    ]);

    return redirect()->route('admin.quizzes.questions', $quiz)
      ->with('success', 'Câu hỏi đã được cập nhật thành công!');
  }

  /**
   * Delete a question.
   */
  public function destroyQuestion(Quiz $quiz, QuizQuestion $question)
  {
    $question->delete();

    return redirect()->route('admin.quizzes.questions', $quiz)
      ->with('success', 'Câu hỏi đã được xóa thành công!');
  }
}
