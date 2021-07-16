<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicPageTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_page_texts', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->unsignedBigInteger('dynamic_page_id');
            $table->boolean('is_current')->default(false);

            $table->foreign('dynamic_page_id')
                ->references('id')
                ->on('dynamic_pages')->onDelete('cascade');

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
        Schema::dropIfExists('dynamic_page_texts');
    }
}
