<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateFieldsToIdeaDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('idea_dates', 'start_datetime_utc')) {
            Schema::table('idea_dates', function (Blueprint $table) {
                $table->dropColumn('start_datetime_utc');
                $table->dropColumn('end_datetime_utc');
                $table->dropColumn('is_all_day');
                $table->dropColumn('is_recurring');
                $table->dropColumn('recurrence_pattern');
                $table->dropColumn('duration');
                $table->date('start_date')->nullable();
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
        Schema::table('idea_dates', function (Blueprint $table) {
            //
        });
    }
}
