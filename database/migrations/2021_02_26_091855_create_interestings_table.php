<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interestings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->longText('short_description');
            $table->unsignedBigInteger('category_interesting_id');
            $table->string('video_iframe')->nullable();

            //SEO fields
            $table->longText('seo_title')->nullable();
            $table->longText('seo_description')->nullable();

            $table->foreign('category_interesting_id')->references('id')->on('category_interestings')->onDelete('cascade');
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
        Schema::dropIfExists('interestings');
    }
}
