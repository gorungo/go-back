<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdeaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ideas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->comment('who is creator of idea');
            $table->integer('category_id')->unsigned()->default(0)->comment('id of category');
            $table->boolean('active')->default(0)->lenght(1)->comment('is idea visible');
            $table->boolean('status')->default(0)->lenght(1)->comment('moderation status');
            $table->string('slug');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                ->references('id')->on('category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ideas');
    }
}
