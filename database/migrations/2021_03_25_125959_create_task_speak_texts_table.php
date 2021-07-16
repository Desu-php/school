<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskSpeakTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_speak_texts', function (Blueprint $table) {
            $table->id();
            $table->text('prompt')->nullable();
            $table->text('answer_text');
            $table->string('audio')->nullable();
            $table->string('video_iframe')->nullable();
            $table->string('video')->nullable();
            $table->unsignedBigInteger('task_id');
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
        Schema::dropIfExists('task_speak_texts');
    }
}
