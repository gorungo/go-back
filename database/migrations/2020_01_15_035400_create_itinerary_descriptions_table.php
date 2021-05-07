<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItineraryDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itinerary_descriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('itinerary_id')->unsigned();
            $table->integer('locale_id')->unsigned()->lenght(2);

            $table->string('title', 191);
            $table->text('description');
            $table->text('what_included')->nullable();
            $table->text('will_visit')->nullable();
            $table->text('note')->nullable();

            $table->foreign('locale_id')->references('id')->on('locales');
            $table->foreign('itinerary_id')->references('id')->on('itineraries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itinerary_descriptions');
    }
}
