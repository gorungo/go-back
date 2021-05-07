<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdeaApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idea_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idea_id');
            $table->unsignedInteger('approval_status_id');
            $table->unsignedInteger('moderator_id');
            $table->timestamp('approved_at');
            $table->timestamps();

            $table->foreign('idea_id')->references('id')->on('ideas');
            $table->foreign('approval_status_id')->references('id')->on('approval_statuses');
            $table->foreign('moderator_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('idea_approvals');
    }
}
