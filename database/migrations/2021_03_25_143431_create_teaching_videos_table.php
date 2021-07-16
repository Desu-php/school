<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachingVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teaching_videos', function (Blueprint $table) {
            $table->id();
            $table->longText('title');
            $table->longText('description')->nullable();
            $table->string('video_iframe')->nullable();
            $table->string('image')->nullable();
            $table->string('image_extension')->nullable();
            $table->string('image_alt')->default('image');
            $table->string('video')->nullable();

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
        Schema::dropIfExists('teaching_videos');
    }
}
