<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonBlockFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_block_files', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('extension');
            $table->string('type'); //gallery - file
            $table->unsignedBigInteger('lesson_block_id')->nullable();

            $table->foreign('lesson_block_id')->references('id')->on('lesson_blocks')->onDelete('cascade');
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
        Schema::dropIfExists('lesson_block_files');
    }
}
