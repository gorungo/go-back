<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Place;
use App\PlaceDescription;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class AddContinents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // north america
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '-119.0197997',
                '36.3039605'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Северная Америка',
            'intro' => 'Северная Америка',
            'description' => 'Северная Америка',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'North America',
            'intro' => 'North America',
            'description' => 'North America',
        ]);

        // south america
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '-100.7375687',
                '-15.5708043'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Южная Америка',
            'intro' => 'Южная Америка',
            'description' => 'Южная Америка',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'South America',
            'intro' => 'South America',
            'description' => 'South America',
        ]);

        // europe
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '4.1425359',
                '48.1275788'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Европа',
            'intro' => 'Европа',
            'description' => 'Европа',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'Europe',
            'intro' => 'Europe',
            'description' => 'Europe',
        ]);

        // africa
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '-16.3209465,3',
                '1.8281813'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Африка',
            'intro' => 'Африка',
            'description' => 'Африка',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'Africa',
            'intro' => 'Africa',
            'description' => 'Africa',
        ]);

        // asia
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '62.3432263',
                '23.775891'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Азия',
            'intro' => 'Азия',
            'description' => 'Азия',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'Asia',
            'intro' => 'Asia',
            'description' => 'Asia',
        ]);

        // oceania
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '91.1492941',
                '-8.5711549'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Океания',
            'intro' => 'Океания',
            'description' => 'Океания',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'Oceania',
            'intro' => 'Oceania',
            'description' => 'Oceania',
        ]);

        // antarctica
        $place = Place::create([
            'place_type_id' => 1,
            'coordinates' => new Point(
                '-73.6320117',
                '-46.3043306'
            ),
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 2,
            'title' => 'Антарктика',
            'intro' => 'Антарктика',
            'description' => 'Антарктика',
        ]);

        $placeDesc = $place->placeDescriptions()->create([
            'locale_id' => 1,
            'title' => 'Antarctica',
            'intro' => 'Antarctica',
            'description' => 'Antarctica',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
