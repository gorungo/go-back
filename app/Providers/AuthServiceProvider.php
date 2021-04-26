<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\Action' => 'App\Policies\ActionPolicy',
        'App\Idea' => 'App\Policies\IdeaPolicy',
        'App\Category' => 'App\Policies\CategoryPolicy',
        'App\Place' => 'App\Policies\PlacePolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\Profile' => 'App\Policies\ProfilePolicy',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            $user->hasRole('super-admin') ? true : null;
        });

    }
}
