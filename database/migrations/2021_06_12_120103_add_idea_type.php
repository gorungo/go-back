<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdeaType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ideas', 'idea_type_id')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->integer('idea_type_id')->default(1)->after('place_id');
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
        if (Schema::hasColumn('ideas', 'idea_type_id')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->dropColumn('idea_type_id');
            });
        }
    }
}
