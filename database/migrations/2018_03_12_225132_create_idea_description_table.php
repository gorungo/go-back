<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdeaDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idea_description', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idea_id')->unsigned();
            $table->integer('locale_id')->unsigned()->lenght(2);
            $table->string('title', 191);
            $table->string('intro', 255);
            $table->text('description');

            $table->foreign('locale_id')->references('id')->on('locale')->onDelete('cascade');
            $table->foreign('idea_id')->references('id')->on('idea')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('idea_description');
    }
}
