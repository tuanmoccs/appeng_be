<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateListeningTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listening_tests', function (Blueprint $table) {
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['ielts', 'toeic', 'toefl', 'general'])->default('general');
            $table->integer('time_limit')->default(30);
            $table->float('passing_score')->default(70);
            $table->integer('total_questions')->default(0);
            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
