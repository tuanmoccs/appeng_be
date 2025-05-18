<?php
namespace App\Services;

use App\Models\Quiz;
use App\Models\UserQuizResult;
use App\Models\UserStat;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;

class QuizService
{
    /**
     * Submit a quiz with user answers.
     *
     * @param int $userId
     * @param int $quizId
     * @param array $userAnswers
     * @return array
     */
    public function submitQuiz($userId, $quizId, $userAnswers)
    {
        // Get the quiz with questions
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        // Calculate score
        $score = 0;
        $totalQuestions = count($quiz->questions);
        $correctAnswers = [];
        $incorrectAnswers = [];

        foreach ($quiz->questions as $question) {
            $questionId = $question->id;

            // Check if user provided an answer for this question
            if (isset($userAnswers[$questionId])) {
                $userAnswer = $userAnswers[$questionId];

                // Check if the answer is correct
                if ($userAnswer === $question->correct_answer) {
                    $score++;
                    $correctAnswers[] = $questionId;
                } else {
                    $incorrectAnswers[] = [
                        'question_id' => $questionId,
                        'user_answer' => $userAnswer,
                        'correct_answer' => $question->correct_answer
                    ];
                }
            } else {
                // User didn't answer this question
                $incorrectAnswers[] = [
                    'question_id' => $questionId,
                    'user_answer' => null,
                    'correct_answer' => $question->correct_answer
                ];
            }
        }

        // Calculate percentage score
        $percentageScore = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

        // Save the result
        $result = DB::transaction(function () use ($userId, $quizId, $score, $totalQuestions, $userAnswers, $percentageScore, $quiz) {
            // Create quiz result
            $quizResult = UserQuizResult::create([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'answers' => $userAnswers,
            ]);

            // Update user stats
            $userStat = UserStat::firstOrCreate(['user_id' => $userId], [
                'words_learned' => 0,
                'lessons_completed' => 0,
                'quizzes_completed' => 0,
                'streak_days' => 0,
            ]);

            $userStat->quizzes_completed += 1;
            $userStat->last_activity_at = now();
            $userStat->save();

            // Check for achievements
            $this->checkForAchievements($userId, $score, $totalQuestions, $userStat);

            return $quizResult;
        });

        return [
            'quiz_id' => $quizId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => $percentageScore,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'passed' => $percentageScore >= 70, // Assuming 70% is passing score
        ];
    }

    /**
     * Check for achievements based on quiz results.
     *
     * @param int $userId
     * @param int $score
     * @param int $totalQuestions
     * @param UserStat $userStat
     * @return void
     */
    private function checkForAchievements($userId, $score, $totalQuestions, $userStat)
    {
        // Perfect score achievement
        if ($score === $totalQuestions && $totalQuestions > 0) {
            UserAchievement::firstOrCreate(
                [
                    'user_id' => $userId,
                    'achievement_type' => 'quiz_perfect',
                ],
                [
                    'title' => 'Perfect Score',
                    'description' => 'You got a perfect score on a quiz!',
                    'achieved_at' => now(),
                ]
            );
        }

        // First quiz achievement
        if ($userStat->quizzes_completed === 1) {
            UserAchievement::firstOrCreate(
                [
                    'user_id' => $userId,
                    'achievement_type' => 'first_quiz',
                ],
                [
                    'title' => 'First Quiz',
                    'description' => 'You completed your first quiz!',
                    'achieved_at' => now(),
                ]
            );
        }

        // 5 quizzes achievement
        if ($userStat->quizzes_completed === 5) {
            UserAchievement::firstOrCreate(
                [
                    'user_id' => $userId,
                    'achievement_type' => 'five_quizzes',
                ],
                [
                    'title' => 'Quiz Master',
                    'description' => 'You completed 5 quizzes!',
                    'achieved_at' => now(),
                ]
            );
        }
    }
}
