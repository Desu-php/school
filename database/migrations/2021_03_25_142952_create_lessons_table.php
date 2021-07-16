<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->longText('description');
            $table->longText('short_description');
            $table->string('video_iframe')->nullable();
            $table->string('video_file')->nullable();
            $table->boolean('is_free');
            $table->unsignedBigInteger('course_module_id')->nullable();


            $table->foreign('course_module_id')->references('id')->on('course_modules')->onDelete('cascade');
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
        Schema::dropIfExists('lessons');
    }
}
