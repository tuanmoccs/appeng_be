<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;

use App\Models\Quiz;
use App\Models\UserQuizResult;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller{
    protected $quizService;
    public function __construct(QuizService $quizService){
        $this->quizService = $quizService;
    }

    public function index(){
        $quizzes = Quiz::with('lesson:id,title')->get();
        return response()->json($quizzes);
    }

    public function show($id){
        $quiz = Quiz::with(['questions' => function ($query) {
            // Don't include correct_answer in the response
            $query->select('id', 'quiz_id', 'question', 'options');
        }])->findOrFail($id);

        return response()->json($quiz);
    }

    public function submit(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get user ID
        $userId = Auth::id();

        // Submit quiz
        $result = $this->quizService->submitQuiz($userId, $id, $request->answers);

        return response()->json($result);
    }

    public function getByLesson($lessonId)
    {
        $quizzes = Quiz::where('lesson_id', $lessonId)->get();
        return response()->json($quizzes);
    }

    public function getUserResults()
    {
        $userId = Auth::id();
        $results = UserQuizResult::with('quiz:id,title,lesson_id')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($results);
    }
}
