<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserListeningResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_listening_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('listening_test_id')->constrained()->onDelete('cascade');
            $table->float('score')->default(0);
            $table->integer('total_questions');
            $table->integer('correct_answers')->default(0);
            $table->integer('time_taken')->default(0); // in seconds
            $table->boolean('completed')->default(false);
            $table->json('answers')->nullable(); // Store user answers
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
        Schema::dropIfExists('user_listening_results');
    }
}
