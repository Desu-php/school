<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     *
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->longText('description');
            $table->unsignedBigInteger('lesson_block_id')->nullable();
            $table->unsignedBigInteger('test_id')->nullable();
            $table->unsignedBigInteger('module_test_id')->nullable();
            $table->unsignedBigInteger('task_type_id');
            $table->string('video')->nullable();
            $table->string('audio')->nullable();
            $table->string('video_iframe')->nullable();
            $table->string('status_task')->default('inactive');
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     *
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
