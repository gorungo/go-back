<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdeaIdFieldToIdeas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ideas', 'idea_id')){
            Schema::table('ideas', function (Blueprint $table) {
                $table->integer('idea_id')
                    ->nullable()
                    ->unsigned()
                    ->after('id');

                $table->foreign('idea_id')->references('id')->on('ideas');
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
        if (!Schema::hasColumn('ideas', 'idea_id')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->dropColumn('idea_id');
            });
        }

    }
}
