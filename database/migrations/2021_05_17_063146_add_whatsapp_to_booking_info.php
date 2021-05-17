<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhatsappToBookingInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('booking_infos', 'whatsapp')) {
            Schema::table('booking_infos', function (Blueprint $table) {
                $table->string('whatsapp', 15)->nullable();
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
        if (!Schema::hasColumn('booking_infos', 'whatsapp')) {
            Schema::table('booking_infos', function (Blueprint $table) {
                $table->dropColumn('whatsapp');
            });
        }
    }
}
