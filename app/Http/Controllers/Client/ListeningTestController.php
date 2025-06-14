<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ListeningTest;
use App\Models\UserListeningResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListeningTestController extends Controller
{
    /**
     * Get all active listening tests
     */
    public function index()
    {
        try {
            $tests = ListeningTest::where('is_active', true)
                ->select('id', 'title', 'description', 'type', 'total_questions', 'time_limit', 'passing_score', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'tests' => $tests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách listening test'
            ], 500);
        }
    }

    /**
     * Get specific listening test with sections and questions
     */
    public function show($id)
    {
        try {
            $test = ListeningTest::with([
                'sections' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.questions' => function ($query) {
                    $query->orderBy('order')
                        ->select('id', 'listening_section_id', 'question', 'options', 'audio_file', 'audio_start_time', 'audio_end_time', 'order');
                }
            ])->findOrFail($id);

            // Process sections and questions
            $test->sections->each(function ($section) {
                $section->questions->each(function ($question) {
                    // Ensure options is an array
                    if (is_string($question->options)) {
                        $question->options = json_decode($question->options, true) ?: [];
                    }
                    
                    // Add full audio URL if exists
                    if ($question->audio_file) {
                        $question->audio_url = asset('storage/' . $question->audio_file);
                    }
                    
                    // Remove correct_answer from response for security
                    unset($question->correct_answer);
                });
                
                // Add full audio URL for section
                if ($section->audio_file) {
                    $section->audio_url = asset('storage/' . $section->audio_file);
                }
            });

            return response()->json([
                'success' => true,
                'test' => $test
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy listening test'
            ], 404);
        }
    }

    /**
     * Submit listening test answers
     */
    public function submit(Request $request, $id)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.selected_answer' => 'required|string',
            'time_taken' => 'nullable|integer'
        ]);

        try {
            DB::beginTransaction();

            $test = ListeningTest::with(['sections.questions'])->findOrFail($id);
            $user = Auth::user();
            
            // Get all questions with correct answers
            $allQuestions = collect();
            foreach ($test->sections as $section) {
                $allQuestions = $allQuestions->merge($section->questions);
            }

            $totalQuestions = $allQuestions->count();
            $correctAnswers = 0;

            // Check answers
            foreach ($request->answers as $answer) {
                $question = $allQuestions->firstWhere('id', $answer['question_id']);
                if ($question && $question->correct_answer === $answer['selected_answer']) {
                    $correctAnswers++;
                }
            }

            $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
            $isPassed = $score >= $test->passing_score;

            // Save result
            $result = UserListeningResult::create([
                'user_id' => $user->id,
                'listening_test_id' => $id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'time_taken' => $request->time_taken,
                'answers' => json_encode($request->answers),
                'passed' => $isPassed
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'result' => [
                    'test_id' => $id,
                    'score' => $score,
                    'is_passed' => $isPassed,
                    'correct_answers' => $correctAnswers,
                    'total_questions' => $totalQuestions,
                    'result_id' => $result->id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể nộp bài listening test'
            ], 500);
        }
    }

    /**
     * Get user listening test results
     */
    public function results($id)
    {
        try {
            $user = Auth::user();
            $results = UserListeningResult::where('user_id', $user->id)
                ->where('listening_test_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải kết quả listening test'
            ], 500);
        }
    }
}
