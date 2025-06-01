<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lessons = [
            [
                'title' => 'Từ vựng cơ bản',
                'description' => 'Học các từ vựng cơ bản trong tiếng Anh hàng ngày',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Chào hỏi cơ bản',
                            'items' => [
                                ['word' => 'Hello', 'meaning' => 'Xin chào', 'example' => 'Hello, how are you?'],
                                ['word' => 'Goodbye', 'meaning' => 'Tạm biệt', 'example' => 'Goodbye, see you tomorrow!'],
                                ['word' => 'Thank you', 'meaning' => 'Cảm ơn', 'example' => 'Thank you for your help.'],
                            ]
                        ],
                        [
                            'title' => 'Đại từ nhân xưng',
                            'items' => [
                                ['word' => 'I', 'meaning' => 'Tôi', 'example' => 'I am a student.'],
                                ['word' => 'You', 'meaning' => 'Bạn', 'example' => 'You are very kind.'],
                                ['word' => 'We', 'meaning' => 'Chúng tôi', 'example' => 'We are learning English.'],
                            ]
                        ]
                    ],
                    'duration' => 15
                ]),
                'level' => 'beginner',
                'duration' => 15,
                'order' => 1,
            ],
            [
                'title' => 'Từ vựng về gia đình',
                'description' => 'Học các từ vựng liên quan đến gia đình và người thân',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Thành viên gia đình',
                            'items' => [
                                ['word' => 'Father', 'meaning' => 'Bố', 'example' => 'My father is a doctor.'],
                                ['word' => 'Mother', 'meaning' => 'Mẹ', 'example' => 'My mother cooks very well.'],
                                ['word' => 'Sibling', 'meaning' => 'Anh/chị/em', 'example' => 'I have two siblings.'],
                            ]
                        ],
                        [
                            'title' => 'Mối quan hệ',
                            'items' => [
                                ['word' => 'Grandfather', 'meaning' => 'Ông', 'example' => 'My grandfather is 80 years old.'],
                                ['word' => 'Aunt', 'meaning' => 'Cô/dì', 'example' => 'My aunt lives in Hanoi.'],
                                ['word' => 'Cousin', 'meaning' => 'Anh/chị em họ', 'example' => 'I play football with my cousin.'],
                            ]
                        ]
                    ],
                    'duration' => 20
                ]),
                'level' => 'beginner',
                'duration' => 20,
                'order' => 2,
            ],
            [
                'title' => 'Từ vựng về thức ăn',
                'description' => 'Học các từ vựng liên quan đến thức ăn và đồ uống',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Đồ ăn',
                            'items' => [
                                ['word' => 'Rice', 'meaning' => 'Cơm', 'example' => 'I eat rice every day.'],
                                ['word' => 'Noodles', 'meaning' => 'Mì/bún/phở', 'example' => 'Pho is a popular noodle soup in Vietnam.'],
                                ['word' => 'Bread', 'meaning' => 'Bánh mì', 'example' => 'I like bread with butter.'],
                            ]
                        ],
                        [
                            'title' => 'Đồ uống',
                            'items' => [
                                ['word' => 'Water', 'meaning' => 'Nước', 'example' => 'Drink more water every day.'],
                                ['word' => 'Coffee', 'meaning' => 'Cà phê', 'example' => 'Vietnamese coffee is very strong.'],
                                ['word' => 'Juice', 'meaning' => 'Nước ép', 'example' => 'Orange juice is my favorite.'],
                            ]
                        ]
                    ],
                    'duration' => 25
                ]),
                'level' => 'beginner',
                'duration' => 25,
                'order' => 3,
            ],
            [
                'title' => 'Từ vựng về công việc',
                'description' => 'Học các từ vựng liên quan đến công việc và nghề nghiệp',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Nghề nghiệp',
                            'items' => [
                                ['word' => 'Teacher', 'meaning' => 'Giáo viên', 'example' => 'My mother is a teacher.'],
                                ['word' => 'Engineer', 'meaning' => 'Kỹ sư', 'example' => 'He works as a software engineer.'],
                                ['word' => 'Doctor', 'meaning' => 'Bác sĩ', 'example' => 'The doctor examined the patient.'],
                            ]
                        ],
                        [
                            'title' => 'Nơi làm việc',
                            'items' => [
                                ['word' => 'Office', 'meaning' => 'Văn phòng', 'example' => 'I go to the office at 8 AM.'],
                                ['word' => 'Factory', 'meaning' => 'Nhà máy', 'example' => 'The factory produces electronic devices.'],
                                ['word' => 'Hospital', 'meaning' => 'Bệnh viện', 'example' => 'She works at the local hospital.'],
                            ]
                        ]
                    ],
                    'duration' => 30
                ]),
                'level' => 'intermediate',
                'duration' => 30,
                'order' => 4,
            ],
            [
                'title' => 'Từ vựng về du lịch',
                'description' => 'Học các từ vựng liên quan đến du lịch và khách sạn',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Phương tiện di chuyển',
                            'items' => [
                                ['word' => 'Airplane', 'meaning' => 'Máy bay', 'example' => 'We will go by airplane to Thailand.'],
                                ['word' => 'Taxi', 'meaning' => 'Taxi', 'example' => 'Let\'s take a taxi to the hotel.'],
                                ['word' => 'Subway', 'meaning' => 'Tàu điện ngầm', 'example' => 'The subway is convenient in big cities.'],
                            ]
                        ],
                        [
                            'title' => 'Khách sạn',
                            'items' => [
                                ['word' => 'Reception', 'meaning' => 'Quầy lễ tân', 'example' => 'Check in at the reception.'],
                                ['word' => 'Reservation', 'meaning' => 'Đặt phòng', 'example' => 'I made a hotel reservation online.'],
                                ['word' => 'Suite', 'meaning' => 'Phòng cao cấp', 'example' => 'They booked a suite for their honeymoon.'],
                            ]
                        ]
                    ],
                    'duration' => 35
                ]),
                'level' => 'intermediate',
                'duration' => 35,
                'order' => 5,
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson);
        }
    }
}
