<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskGallowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_gallows', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->text('prompt')->nullable();
            $table->string('amount_numbers')->nullable();
            $table->unsignedBigInteger('task_id');
            $table->boolean('select_dictionary');
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
        Schema::dropIfExists('task_gallows');
    }
}
