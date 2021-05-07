<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\PlaceType;
use App\PlaceTypeDescription;

class AddRegionsAndCitiesToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $placeType = PlaceType::create([
            'place_type_group_id' => 1,
            'slug' => 'settlement'
        ]);

        PlaceTypeDescription::create([
            'place_type_id' => $placeType->id,
            'locale_id' => 1,
            'title' => 'settlement'
        ]);

        PlaceTypeDescription::create([
            'place_type_id' => $placeType->id,
            'locale_id' => 2,
            'title' => 'поселение'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table', function (Blueprint $table) {
            //
        });
    }
}
