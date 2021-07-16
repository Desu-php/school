<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_blocks', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->longText('description');
            $table->unsignedBigInteger('lesson_id');
            $table->string('gallery')->nullable();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
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
        Schema::dropIfExists('lesson_blocks');
    }
}
