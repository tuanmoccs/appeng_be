<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentPositionToUserLessonProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_lesson_progress', function (Blueprint $table) {
            $table->integer('current_section')->default(0)->after('progress_percentage');
            $table->integer('current_item')->default(0)->after('current_section');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_lesson_progress', function (Blueprint $table) {
            $table->dropColumn(['current_section', 'current_item']);
        });
    }
}
