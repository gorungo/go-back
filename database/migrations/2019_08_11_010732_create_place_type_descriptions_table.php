<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceTypeDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_type_descriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_type_id')->unsigned();
            $table->integer('locale_id')->unsigned();
            $table->string('title',100);

            $table->foreign('place_type_id')->references('id')->on('place_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('place_type_descriptions');
    }
}
