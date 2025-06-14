<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListeningQuestion;

class ListeningQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            [
                'listening_section_id' => 1,
                'question' => 'What is the main topic of the conversation?',
                'options' => [
                    'A) Planning a vacation',
                    'B) Discussing work schedules',
                    'C) Ordering food at a restaurant',
                    'D) Talking about the weather'
                ],
                'correct_answer' => 'A',
                'audio_file' => 'listening_audio_1.mp3',
                'audio_start_time' => 0,
                'audio_end_time' => 30,
                'order' => 1,
            ],
            [
                'listening_section_id' => 1,
                'question' => 'Where does the woman want to go for vacation?',
                'options' => [
                    'A) Paris, France',
                    'B) Tokyo, Japan',
                    'C) New York, USA',
                    'D) London, England'
                ],
                'correct_answer' => 'B',
                'audio_file' => 'listening_audio_1.mp3',
                'audio_start_time' => 15,
                'audio_end_time' => 45,
                'order' => 2,
            ],
            [
                'listening_section_id' => 1,
                'question' => 'How long does the man suggest staying?',
                'options' => [
                    'A) One week',
                    'B) Two weeks',
                    'C) Three weeks',
                    'D) One month'
                ],
                'correct_answer' => 'B',
                'audio_file' => 'listening_audio_1.mp3',
                'audio_start_time' => 30,
                'audio_end_time' => 60,
                'order' => 3,
            ],
            [
                'listening_section_id' => 1,
                'question' => 'What is the woman\'s main concern about the trip?',
                'options' => [
                    'A) The cost of the trip',
                    'B) The language barrier',
                    'C) The weather conditions',
                    'D) The flight duration'
                ],
                'correct_answer' => 'A',
                'audio_file' => 'listening_audio_1.mp3',
                'audio_start_time' => 45,
                'audio_end_time' => 75,
                'order' => 4,
            ],
            [
                'listening_section_id' => 1,
                'question' => 'When do they plan to book the tickets?',
                'options' => [
                    'A) Today',
                    'B) Tomorrow',
                    'C) Next week',
                    'D) Next month'
                ],
                'correct_answer' => 'C',
                'audio_file' => 'listening_audio_1.mp3',
                'audio_start_time' => 60,
                'audio_end_time' => 90,
                'order' => 5,
            ],
        ];

        foreach ($questions as $questionData) {
            ListeningQuestion::create($questionData);
        }
    }
}
