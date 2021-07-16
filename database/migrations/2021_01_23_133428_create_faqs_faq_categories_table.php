<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsFaqCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs_faq_categories', function (Blueprint $table) {
          $table->id();

          $table->unsignedBigInteger('faq_category_id');
          $table->unsignedBigInteger('faq_id');

          $table->foreign('faq_category_id')
             ->references('id')
             ->on('faq_categories')->onDelete('cascade');
          $table->foreign('faq_id')
             ->references('id')
             ->on('faqs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs_faq_categories');
    }
}
