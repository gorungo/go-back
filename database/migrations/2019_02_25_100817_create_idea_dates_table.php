<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdeaDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idea_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idea_id')->unsigned();
            $table->dateTime('start_datetime_utc');
            $table->dateTime('end_datetime_utc')->nullable();
            $table->boolean('is_all_day')->default(0);
            $table->integer('time_zone_offset')->nullable();
            $table->integer('duration');
            $table->integer('is_recurring')->default(0);
            $table->string('recurrence_pattern', 255)->nullable();

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
        Schema::dropIfExists('idea_dates');
    }
}
