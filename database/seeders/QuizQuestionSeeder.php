<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các quiz
        $quizIds = Quiz::pluck('id')->toArray();

        $questions = [
            // Quiz 1: Từ vựng cơ bản
            [
                'quiz_id' => $quizIds[0],
                'question' => 'Từ "hello" có nghĩa là gì?',
                'options' => json_encode(['xin chào', 'tạm biệt', 'cảm ơn', 'xin lỗi']),
                'correct_answer' => 'xin chào',
            ],
            [
                'quiz_id' => $quizIds[0],
                'question' => 'Từ nào dưới đây có nghĩa là "tạm biệt"?',
                'options' => json_encode(['hello', 'goodbye', 'thank you', 'sorry']),
                'correct_answer' => 'goodbye',
            ],
            [
                'quiz_id' => $quizIds[0],
                'question' => 'Từ "thank you" có nghĩa là gì?',
                'options' => json_encode(['xin chào', 'tạm biệt', 'cảm ơn', 'xin lỗi']),
                'correct_answer' => 'cảm ơn',
            ],
            [
                'quiz_id' => $quizIds[0],
                'question' => 'Cách phát âm đúng của từ "hello" là gì?',
                'options' => json_encode(['həˈloʊ', 'heˈlo', 'heˈləʊ', 'həˈləʊ']),
                'correct_answer' => 'həˈloʊ',
            ],
            [
                'quiz_id' => $quizIds[0],
                'question' => 'Chọn câu dịch đúng: "Hello, how are you today?"',
                'options' => json_encode([
                    'Xin chào, hôm nay bạn khỏe không?',
                    'Xin chào, hôm qua bạn khỏe không?',
                    'Tạm biệt, hôm nay bạn khỏe không?',
                    'Xin chào, ngày mai bạn khỏe không?'
                ]),
                'correct_answer' => 'Xin chào, hôm nay bạn khỏe không?',
            ],

            // Quiz 2: Từ vựng về gia đình
            [
                'quiz_id' => $quizIds[1],
                'question' => 'Từ "father" có nghĩa là gì?',
                'options' => json_encode(['cha, bố', 'mẹ', 'anh trai', 'em gái']),
                'correct_answer' => 'cha, bố',
            ],
            [
                'quiz_id' => $quizIds[1],
                'question' => 'Từ nào dưới đây có nghĩa là "mẹ"?',
                'options' => json_encode(['father', 'mother', 'brother', 'sister']),
                'correct_answer' => 'mother',
            ],
            [
                'quiz_id' => $quizIds[1],
                'question' => 'Từ "sister" có nghĩa là gì?',
                'options' => json_encode(['anh trai', 'em trai', 'chị/em gái', 'cha, bố']),
                'correct_answer' => 'chị/em gái',
            ],
            [
                'quiz_id' => $quizIds[1],
                'question' => 'Cách phát âm đúng của từ "mother" là gì?',
                'options' => json_encode(['ˈmʌðər', 'ˈmoʊðər', 'ˈmɑːðər', 'ˈmʊðər']),
                'correct_answer' => 'ˈmʌðər',
            ],
            [
                'quiz_id' => $quizIds[1],
                'question' => 'Chọn câu dịch đúng: "My father works as a doctor."',
                'options' => json_encode([
                    'Bố tôi làm bác sĩ.',
                    'Mẹ tôi làm bác sĩ.',
                    'Bố tôi làm giáo viên.',
                    'Bố tôi đã từng làm bác sĩ.'
                ]),
                'correct_answer' => 'Bố tôi làm bác sĩ.',
            ],

            // Quiz 3: Từ vựng về thức ăn
            [
                'quiz_id' => $quizIds[2],
                'question' => 'Từ "rice" có nghĩa là gì?',
                'options' => json_encode(['gạo, cơm', 'bánh mì', 'nước', 'trái cây']),
                'correct_answer' => 'gạo, cơm',
            ],
            [
                'quiz_id' => $quizIds[2],
                'question' => 'Từ nào dưới đây có nghĩa là "nước"?',
                'options' => json_encode(['rice', 'bread', 'water', 'fruit']),
                'correct_answer' => 'water',
            ],
            [
                'quiz_id' => $quizIds[2],
                'question' => 'Từ "fruit" có nghĩa là gì?',
                'options' => json_encode(['rau', 'thịt', 'cá', 'trái cây']),
                'correct_answer' => 'trái cây',
            ],
            [
                'quiz_id' => $quizIds[2],
                'question' => 'Cách phát âm đúng của từ "water" là gì?',
                'options' => json_encode(['ˈwɔːtər', 'ˈwætər', 'ˈwaɪtər', 'ˈwoʊtər']),
                'correct_answer' => 'ˈwɔːtər',
            ],
            [
                'quiz_id' => $quizIds[2],
                'question' => 'Chọn câu dịch đúng: "Eating fruit is good for your health."',
                'options' => json_encode([
                    'Ăn trái cây tốt cho sức khỏe của bạn.',
                    'Ăn rau tốt cho sức khỏe của bạn.',
                    'Ăn trái cây không tốt cho sức khỏe của bạn.',
                    'Ăn trái cây tốt cho làn da của bạn.'
                ]),
                'correct_answer' => 'Ăn trái cây tốt cho sức khỏe của bạn.',
            ],

            // Quiz 4: Từ vựng về công việc
            [
                'quiz_id' => $quizIds[3],
                'question' => 'Từ "job" có nghĩa là gì?',
                'options' => json_encode(['công việc', 'văn phòng', 'cuộc họp', 'đồng nghiệp']),
                'correct_answer' => 'công việc',
            ],
            [
                'quiz_id' => $quizIds[3],
                'question' => 'Từ nào dưới đây có nghĩa là "văn phòng"?',
                'options' => json_encode(['job', 'office', 'meeting', 'colleague']),
                'correct_answer' => 'office',
            ],
            [
                'quiz_id' => $quizIds[3],
                'question' => 'Từ "meeting" có nghĩa là gì?',
                'options' => json_encode(['công việc', 'văn phòng', 'cuộc họp', 'đồng nghiệp']),
                'correct_answer' => 'cuộc họp',
            ],
            [
                'quiz_id' => $quizIds[3],
                'question' => 'Cách phát âm đúng của từ "office" là gì?',
                'options' => json_encode(['ˈɔːfɪs', 'ˈɒfɪs', 'oʊˈfɪs', 'ˈoʊfɪs']),
                'correct_answer' => 'ˈɔːfɪs',
            ],
            [
                'quiz_id' => $quizIds[3],
                'question' => 'Chọn câu dịch đúng: "We have a team meeting every Monday morning."',
                'options' => json_encode([
                    'Chúng tôi có cuộc họp nhóm vào mỗi sáng thứ Hai.',
                    'Chúng tôi có cuộc họp nhóm vào mỗi chiều thứ Hai.',
                    'Chúng tôi có cuộc họp nhóm vào mỗi sáng thứ Ba.',
                    'Chúng tôi đã có cuộc họp nhóm vào sáng thứ Hai.'
                ]),
                'correct_answer' => 'Chúng tôi có cuộc họp nhóm vào mỗi sáng thứ Hai.',
            ],

            // Quiz 5: Từ vựng về du lịch
            [
                'quiz_id' => $quizIds[4],
                'question' => 'Từ "hotel" có nghĩa là gì?',
                'options' => json_encode(['khách sạn', 'hộ chiếu', 'vali', 'máy bay']),
                'correct_answer' => 'khách sạn',
            ],
            [
                'quiz_id' => $quizIds[4],
                'question' => 'Từ nào dưới đây có nghĩa là "hộ chiếu"?',
                'options' => json_encode(['hotel', 'passport', 'suitcase', 'airplane']),
                'correct_answer' => 'passport',
            ],
            [
                'quiz_id' => $quizIds[4],
                'question' => 'Từ "suitcase" có nghĩa là gì?',
                'options' => json_encode(['khách sạn', 'hộ chiếu', 'vali', 'máy bay']),
                'correct_answer' => 'vali',
            ],
            [
                'quiz_id' => $quizIds[4],
                'question' => 'Cách phát âm đúng của từ "passport" là gì?',
                'options' => json_encode(['ˈpæspɔːrt', 'ˈpɑːspɔːrt', 'pæsˈpɔːrt', 'ˈpæspɔːt']),
                'correct_answer' => 'ˈpæspɔːrt',
            ],
            [
                'quiz_id' => $quizIds[4],
                'question' => 'Chọn câu dịch đúng: "Don\'t forget to bring your passport when traveling abroad."',
                'options' => json_encode([
                    'Đừng quên mang theo hộ chiếu khi đi du lịch nước ngoài.',
                    'Đừng quên mang theo vali khi đi du lịch nước ngoài.',
                    'Đừng quên mang theo hộ chiếu khi đi du lịch trong nước.',
                    'Hãy nhớ mang theo hộ chiếu khi đi du lịch nước ngoài.'
                ]),
                'correct_answer' => 'Đừng quên mang theo hộ chiếu khi đi du lịch nước ngoài.',
            ],
        ];

        foreach ($questions as $question) {
            QuizQuestion::create($question);
        }
    }
}
