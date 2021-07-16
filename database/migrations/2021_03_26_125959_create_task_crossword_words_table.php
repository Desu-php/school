<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskCrosswordWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_crossword_words', function (Blueprint $table) {
            $table->id();
            $table->text('word');
            $table->text('question');
            $table->unsignedBigInteger('crossword_id');

            $table->foreign('crossword_id')->references('id')->on('task_crosswords')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('task_crossword_words');
    }
}
