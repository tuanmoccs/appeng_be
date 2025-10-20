<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Quiz;
use App\Models\UserQuizResult;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class QuizController extends Controller
{
    protected $quizService;
    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index(Request $request)
    {
        $quizzes = Quiz::with('lesson:id,title')->get();
        $user = null;
        $token = $request->bearerToken();

        if ($token) {
            try {
                // Nếu dùng JWT
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                // Token invalid, continue as guest
                $user = null;
            }
        }

        // Nếu có user, lấy kết quả gần nhất
        if ($user) {
            $userId = $user->id;

            $quizzes->transform(function ($quiz) use ($userId) {
                $latestResult = UserQuizResult::where('user_id', $userId)
                    ->where('quiz_id', $quiz->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $quiz->user_latest_result = $latestResult ? [
                    'score' => $latestResult->score,
                    'completed_at' => $latestResult->created_at,
                    'total_questions' => $latestResult->total_questions
                ] : null;

                return $quiz;
            });
        }
        return response()->json($quizzes);
    }

    public function show($id)
    {
        $quiz = Quiz::with(['questions' => function ($query) {
            // Don't include correct_answer in the response
            $query->select('id', 'quiz_id', 'question', 'options');
        }])->findOrFail($id);

        return response()->json($quiz);
    }

    public function submit(Request $request, $id)
    {
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
