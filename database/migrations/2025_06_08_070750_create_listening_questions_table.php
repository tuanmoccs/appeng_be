<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListeningQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listening_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listening_section_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->json('options');
            $table->string('correct_answer');
            $table->string('audio_file')->nullable(); // For single question type
            $table->integer('audio_start_time')->nullable(); // For multiple question type (in seconds)
            $table->integer('audio_end_time')->nullable(); // For multiple question type (in seconds)
            $table->integer('order')->default(1);
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
        Schema::dropIfExists('listening_questions');
    }
}
