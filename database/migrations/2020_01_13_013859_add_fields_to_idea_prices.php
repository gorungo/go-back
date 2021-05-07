<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToIdeaPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('idea_prices', 'idea_date_id')) {
            Schema::table('idea_prices', function (Blueprint $table) {

                $table->integer('idea_price_type_id')
                    ->nullable()
                    ->unsigned()
                    ->after('idea_id')
                    ->comment('for group or for person');

                $table->integer('age_group_id')
                    ->nullable()
                    ->unsigned()
                    ->after('idea_id')
                    ->comment('for group or for person');

                $table->integer('idea_date_id')
                    ->nullable()
                    ->unsigned()
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
        if (Schema::hasColumn('idea_prices', 'idea_date_id')) {
            Schema::table('idea_prices', function (Blueprint $table) {
                $table->dropColumn('idea_date_id');
                $table->dropColumn('age_group_id');
                $table->dropColumn('idea_price_type_id');
            });
        }
    }
}
