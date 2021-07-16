<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplateTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template_texts', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->longText('value');
            $table->unsignedBigInteger('email_template_id');

            $table->foreign('email_template_id')->references('id')->on('email_templates')->onDelete('cascade');
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
        Schema::dropIfExists('email_template_texts');
    }
}
