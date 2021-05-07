<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Currency;

class AddCurrenciesSeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Currency::all()->count()){
            //Artisan::call('db:seed', array('--class' => 'CurrenciesTableSeeder'));
            //Artisan::call('db:seed', array('--class' => 'CurrencyDescriptionsTableSeeder'));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            //
        });
    }
}
