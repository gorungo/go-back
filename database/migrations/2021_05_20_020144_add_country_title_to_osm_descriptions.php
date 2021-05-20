<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\OSMDescription;

class AddCountryTitleToOsmDescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('osm_descriptions', 'country_title')) {
            Schema::table('osm_descriptions', function (Blueprint $table) {
                $table->string('country_title', 50)->nullable()->after('title');
            });
        }

        $osm = OSMDescription::all();
        foreach($osm as $r){
            $nameExplode = explode(',', $r->display_name);
            if(count($nameExplode) > 0){
                $countryTitle = $nameExplode[count($nameExplode) -1];
                $r->country_title = $countryTitle;
                $r->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('osm_descriptions', 'country_title')) {
            Schema::table('osm_descriptions', function (Blueprint $table) {
                $table->dropColumn('country_title');
            });
        }
    }
}
