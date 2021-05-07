<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovedToIdeas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ideas', 'is_approved')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->boolean('is_approved')->after('active')->default(0);
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
        if (Schema::hasColumn('ideas', 'is_approved')) {
            Schema::table('ideas', function (Blueprint $table) {
                $table->dropColumn('is_approved');
            });
        };
    }
}
