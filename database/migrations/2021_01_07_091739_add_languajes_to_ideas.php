<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguajesToIdeas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ideas', 'options')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->json('options')->after('slug')->nullable();
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
        if (Schema::hasColumn('ideas', 'options')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->dropColumn('options');
            });
        };
    }
}
