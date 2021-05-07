<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idea_id')->unsigned()->default(0)->comment('id of idea');
            $table->tinyInteger('day_num')->lenght(2)->comment('activity day number');
            $table->tinyInteger('day_order')->default(1)->lenght(2)->comment('order activities in day');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('idea_id')->references('id')->on('ideas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itineraries');
    }
}
