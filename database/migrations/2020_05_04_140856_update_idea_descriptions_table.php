<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIdeaDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('idea_descriptions', function (Blueprint $table) {
            $table->string('title', 191)->nullable()->change();
            $table->string('intro', 255)->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('idea_descriptions', function (Blueprint $table) {
            $table->string('title', 191)->change();
            $table->string('intro', 255)->change();
            $table->text('description')->change();
        });
    }
}
