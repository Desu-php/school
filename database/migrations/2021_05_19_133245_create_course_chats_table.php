<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_chats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('count_persons')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('chat_type')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
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
        Schema::dropIfExists('course_chats');
    }
}
