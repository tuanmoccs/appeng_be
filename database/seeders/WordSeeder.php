<?php

namespace Database\Seeders;

use App\Models\Word;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các bài học
        $lessonIds = Lesson::pluck('id')->toArray();

        $words = [
            // Bài học 1: Từ vựng cơ bản
            [
                'lesson_id' => $lessonIds[0],
                'word' => 'hello',
                'pronunciation' => 'həˈloʊ',
                'translation' => 'xin chào',
                'example_sentence' => 'Hello, how are you today?',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[0],
                'word' => 'goodbye',
                'pronunciation' => 'ˌɡʊdˈbaɪ',
                'translation' => 'tạm biệt',
                'example_sentence' => 'Goodbye, see you tomorrow!',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[0],
                'word' => 'thank you',
                'pronunciation' => 'θæŋk ju',
                'translation' => 'cảm ơn',
                'example_sentence' => 'Thank you for your help.',
                'image_url' => null,
                'audio_url' => null,
            ],

            // Bài học 2: Từ vựng về gia đình
            [
                'lesson_id' => $lessonIds[1],
                'word' => 'father',
                'pronunciation' => 'ˈfɑːðər',
                'translation' => 'cha, bố',
                'example_sentence' => 'My father works as a doctor.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[1],
                'word' => 'mother',
                'pronunciation' => 'ˈmʌðər',
                'translation' => 'mẹ',
                'example_sentence' => 'My mother is a teacher.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[1],
                'word' => 'sister',
                'pronunciation' => 'ˈsɪstər',
                'translation' => 'chị/em gái',
                'example_sentence' => 'I have two sisters.',
                'image_url' => null,
                'audio_url' => null,
            ],

            // Bài học 3: Từ vựng về thức ăn
            [
                'lesson_id' => $lessonIds[2],
                'word' => 'rice',
                'pronunciation' => 'raɪs',
                'translation' => 'gạo, cơm',
                'example_sentence' => 'Rice is a staple food in many Asian countries.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[2],
                'word' => 'water',
                'pronunciation' => 'ˈwɔːtər',
                'translation' => 'nước',
                'example_sentence' => 'I drink eight glasses of water every day.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[2],
                'word' => 'fruit',
                'pronunciation' => 'fruːt',
                'translation' => 'trái cây',
                'example_sentence' => 'Eating fruit is good for your health.',
                'image_url' => null,
                'audio_url' => null,
            ],

            // Bài học 4: Từ vựng về công việc
            [
                'lesson_id' => $lessonIds[3],
                'word' => 'job',
                'pronunciation' => 'dʒɑːb',
                'translation' => 'công việc',
                'example_sentence' => 'She has a new job at a software company.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[3],
                'word' => 'office',
                'pronunciation' => 'ˈɔːfɪs',
                'translation' => 'văn phòng',
                'example_sentence' => 'The office is located in the city center.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[3],
                'word' => 'meeting',
                'pronunciation' => 'ˈmiːtɪŋ',
                'translation' => 'cuộc họp',
                'example_sentence' => 'We have a team meeting every Monday morning.',
                'image_url' => null,
                'audio_url' => null,
            ],

            // Bài học 5: Từ vựng về du lịch
            [
                'lesson_id' => $lessonIds[4],
                'word' => 'hotel',
                'pronunciation' => 'hoʊˈtel',
                'translation' => 'khách sạn',
                'example_sentence' => 'We stayed at a five-star hotel during our vacation.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[4],
                'word' => 'passport',
                'pronunciation' => 'ˈpæspɔːrt',
                'translation' => 'hộ chiếu',
                'example_sentence' => 'Don\'t forget to bring your passport when traveling abroad.',
                'image_url' => null,
                'audio_url' => null,
            ],
            [
                'lesson_id' => $lessonIds[4],
                'word' => 'suitcase',
                'pronunciation' => 'ˈsuːtkeɪs',
                'translation' => 'vali',
                'example_sentence' => 'I packed all my clothes in a large suitcase.',
                'image_url' => null,
                'audio_url' => null,
            ],
        ];

        foreach ($words as $word) {
            Word::create($word);
        }
    }
}
