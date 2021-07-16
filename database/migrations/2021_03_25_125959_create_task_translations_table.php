<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_translations', function (Blueprint $table) {
            $table->id();
            $table->text('translation');
            $table->text('phrase');
            $table->unsignedBigInteger('pick_up_translation_id');

            $table->foreign('pick_up_translation_id')->references('id')->on('task_pick_up_translations')->onDelete('cascade');
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
        Schema::dropIfExists('task_translations');
    }
}
