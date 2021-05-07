<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('author_id')->unsigned()->comment('who is creator');
                $table->integer('main_category_id')->unsigned()->default(0)->comment('id of category');
                $table->boolean('active')->default(0)->lenght(1)->comment('is visible');
                $table->boolean('status')->default(0)->lenght(1)->comment('moderation status');
                $table->string('slug');
                $table->string('image_name', 20);

                $table->timestamps();
                $table->softDeletes();


            });
            Schema::table('posts', function (Blueprint $table) {
                $table->foreign('main_category_id')
                    ->references('id')->on('post_categories');
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
        Schema::dropIfExists('posts');
    }
}
