<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price'); //price for a days
            $table->integer('duration'); //days
            $table->string('access_extend');
            $table->boolean('automatic_check_tasks');
            $table->integer('freezing_possibility')->nullable(); //days
            $table->boolean('access_independent_work');
            $table->boolean('access_additional_materials');
            $table->boolean('access_dictionary');
            $table->boolean('access_grammar');
            $table->boolean('access_chat');
            $table->boolean('access_fb_chat');
            $table->boolean('access_notes');
            $table->boolean('feedback_experts');
            $table->string('access_upgrade_tariff')->nullable();
            $table->string('access_materials_after_purchasing_course')->nullable(); // month
            $table->string('discount_for_family')->nullable(); // percent
            $table->string('consultation')->nullable();
            $table->boolean('additional_course_gift');

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
        Schema::dropIfExists('course_tariffs');
    }
}
