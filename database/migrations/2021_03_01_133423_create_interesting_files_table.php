<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestingFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interesting_files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('extension')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('interesting_id');

            $table->foreign('interesting_id')->references('id')->on('interestings')->onDelete('cascade');
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
        Schema::dropIfExists('interesting_files');
    }
}
