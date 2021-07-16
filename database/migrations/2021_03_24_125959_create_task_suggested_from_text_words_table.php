<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskSuggestedFromTextWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_suggested_from_text_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('number');
            $table->boolean('word_select');
            $table->unsignedBigInteger('suggested_text_id');

            $table->foreign('suggested_text_id')->references('id')->on('task_suggested_from_text_words')->onDelete('cascade');
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
        Schema::dropIfExists('task_suggested_from_text_words');
    }
}
