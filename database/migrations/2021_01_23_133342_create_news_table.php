<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->longText('title');
            $table->longText('short_description');
            $table->longText('description');
            $table->string('image')->nullable();
            $table->string('image_extension')->nullable();
            $table->string('image_alt')->default('image');
            $table->string('video')->nullable();
            $table->string('video_iframe')->nullable();

            //SEO fields
            $table->longText('seo_title')->nullable();
            $table->longText('seo_description')->nullable();

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
        Schema::dropIfExists('news');
    }
}
