<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRememberFindWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_remember_find_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->unsignedBigInteger('remember_find_id');

            $table->foreign('remember_find_id')->references('id')->on('task_remember_finds')->onDelete('cascade');
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
        Schema::dropIfExists('task_remember_find_words');
    }
}
