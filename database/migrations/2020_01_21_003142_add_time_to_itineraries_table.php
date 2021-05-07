<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeToItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('itineraries', 'start_time')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->time('start_time')
                    ->nullable()
                    ->after('idea_id')
                    ->comment('start time');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('itineraries', 'start_time')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->dropColumn('start_time');
            });
        }
    }
}
