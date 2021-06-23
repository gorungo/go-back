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
            'Category' => 'App\Models\Category',
            'Idea' => 'App\Models\Idea',
            'Photo' => 'App\Models\Photo',
            'Action' => 'App\Models\Action',
            'Place' => 'App\Models\Place',
            'Profile' => 'App\Models\Profile',
            'User' => 'App\Models\User',
            'Helper' => 'App\Classes\Helper',

            // 'как-храним' => 'класс',
        ]);
    }
}
