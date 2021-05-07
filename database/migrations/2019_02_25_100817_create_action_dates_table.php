<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_id')->unsigned();
            $table->dateTime('start_date_utc');
            $table->dateTime('end_date_utc')->nullable();
            $table->boolean('is_all_day')->default(0);
            $table->integer('duration');
            $table->integer('is_recurring')->default(0);
            $table->string('recurrence_pattern', 255);

            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_dates');
    }
}
