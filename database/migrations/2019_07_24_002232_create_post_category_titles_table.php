<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCategoryTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_category_titles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_category_id')->unsigned();
            $table->integer('locale_id')->unsigned()->lenght(2);

            $table->string('title', 191);
            $table->string('intro', 255);

            $table->foreign('post_category_id')
                ->references('id')->on('post_categories')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_category_titles');
    }
}
