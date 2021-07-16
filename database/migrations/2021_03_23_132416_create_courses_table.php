<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->longText('description');
            $table->string('image')->nullable();
            $table->text('course_type');
            $table->boolean('is_free');
            $table->boolean('is_free_lesson');
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('announcement_id')->nullable();
            $table->unsignedBigInteger('course_level_id')->nullable();
            $table->unsignedBigInteger('teaching_language_id');


            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->foreign('teaching_language_id')->references('id')->on('teaching_languages')->onDelete('cascade');
            $table->foreign('course_level_id')->references('id')->on('course_levels')->onDelete('cascade');
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
        Schema::dropIfExists('courses');
    }
}
