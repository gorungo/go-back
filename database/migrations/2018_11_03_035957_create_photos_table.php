<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name')->comment('название файла с расширением');
            $table->integer('item_id')->comment('id элемента, которому принадлежит картинка');
            $table->string('item_type')->comment('тип элемента, которому принадлежит картинка');
            $table->integer('order')->default(0)->comment('тип элемента, которому принадлежит картинка');
            $table->boolean('active')->default(1)->comment('флаг отображения');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photos');
    }
}
