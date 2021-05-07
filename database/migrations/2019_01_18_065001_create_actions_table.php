<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->comment('who is creator of idea');
            $table->integer('idea_id')->unsigned()->default(0)->comment('id of idea');
            $table->boolean('active')->default(0)->lenght(1)->comment('is idea visible');
            $table->string('slug');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('idea_id')->references('id')->on('ideas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}
