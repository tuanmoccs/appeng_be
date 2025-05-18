<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('content')->nullable(); // Lưu nội dung bài học dạng JSON
            $table->string('level')->nullable(); // Beginner, Intermediate, Advanced
            $table->integer('duration')->default(0)->nullable(); // Thời gian ước tính để hoàn thành (phút)
            $table->integer('order')->default(0)->nullable(); // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}
