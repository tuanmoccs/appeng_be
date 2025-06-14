<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListeningSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listening_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listening_test_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->string('audio_file');
            $table->integer('audio_duration')->default(0); // in seconds
            $table->enum('question_type', ['single', 'multiple'])->default('single');
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
        Schema::dropIfExists('listening_sections');
    }
}
