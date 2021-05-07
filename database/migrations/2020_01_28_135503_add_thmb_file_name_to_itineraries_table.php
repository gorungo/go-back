<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThmbFileNameToItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('itineraries', 'thmb_file_name')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->string('thmb_file_name', 20)
                    ->nullable()
                    ->after('idea_id');
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
        if (Schema::hasColumn('itineraries', 'thmb_file_name')) {
            Schema::table('itineraries', function (Blueprint $table) {
                $table->dropColumn('thmb_file_name');
            });
        }
    }
}
