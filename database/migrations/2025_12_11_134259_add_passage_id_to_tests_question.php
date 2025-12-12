<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPassageIdToTestsQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->foreignId('passage_id')->nullable()->after('test_id')
                ->constrained('test_passages')->onDelete('cascade');
            $table->enum('type', ['standalone', 'passage'])->default('standalone')->after('passage_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropForeign(['passage_id']);
            $table->dropColumn(['passage_id', 'type']);
        });
    }
}
