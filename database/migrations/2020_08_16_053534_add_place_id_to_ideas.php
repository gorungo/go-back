<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Idea;

class AddPlaceIdToIdeas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ideas', 'place_id')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->unsignedInteger('place_id')->after('main_category_id')->nullable();
            });
        }

        $ideas = Idea::has('ideaPlaces')->get();

        foreach($ideas as $idea){
            $idea->idea_id = $idea->ideaPlace()->id;
            $idea->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('ideas', 'place_id')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->dropColumn('place_id');
            });
        };
    }
}
