<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToOsms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('osms', 'parent_id')){
            Schema::table('osms', function (Blueprint $table) {
                $table->integer('parent_id')->nullable()->after('id');
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
        if (Schema::hasColumn('osms', 'parent_id')) {
            Schema::table('osms', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }
}
