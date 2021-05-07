<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->comment('who is creator');
            $table->integer('parent_id')->default(0)->comment('parent id');
            $table->boolean('active')->default(0)->lenght(1)->comment('is category visible and active');
            $table->integer('order')->default(0)->lenght(2)->comment('the order of category in categories list');
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
        Schema::dropIfExists('post_categories');
    }
}
