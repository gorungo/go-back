<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaceTypeGroupIdToPlaceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('place_types', function (Blueprint $table) {
            $table->integer('place_type_group_id')
                ->nullable()
                ->unsigned()
                ->after('id');
        });

        Schema::table('place_types', function (Blueprint $table) {
            $table->foreign('place_type_group_id')->references('id')->on('place_type_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('place_types', function (Blueprint $table) {
            //
        });
    }
}
