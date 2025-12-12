<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLessonQuizResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_lesson_quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0); // Điểm số (0-100)
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('time_taken')->nullable(); // Thời gian làm bài (giây)
            $table->json('answers')->nullable(); // Lưu câu trả lời chi tiết
            $table->boolean('is_passed')->default(false); // Đạt >= 80%
            $table->integer('attempt_number')->default(1); // Số lần làm bài
            $table->timestamps();

            // Index để query nhanh
            $table->index(['user_id', 'lesson_id']);
            $table->index('is_passed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_lesson_quiz_results');
    }
}
