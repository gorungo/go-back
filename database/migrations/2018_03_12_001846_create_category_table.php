<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->comment('who is creator of idea');
            $table->integer('parent_id')->default(0)->comment('parent id of idea');
            $table->boolean('active')->default(0)->lenght(1)->comment('is idea visible');
            $table->integer('order')->default(0)->lenght(2)->comment('the order of idea in ideas list');
            $table->string('slug');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category');
    }
}
