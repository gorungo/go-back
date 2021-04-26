<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Relation::morphMap([
            'Category' => 'App\Category',
            'Idea' => 'App\Idea',
            'Photo' => 'App\Photo',
            'Action' => 'App\Action',
            'Place' => 'App\Place',
            'Profile' => 'App\Profile',
            'User' => 'App\User',
            'Helper' => 'App\Helper',

            // 'как-храним' => 'класс',
        ]);
    }
}
