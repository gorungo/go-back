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
        'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Action' => 'App\Policies\ActionPolicy',
        'App\Models\Idea' => 'App\Policies\IdeaPolicy',
        'App\Models\Category' => 'App\Policies\CategoryPolicy',
        'App\Models\Place' => 'App\Policies\PlacePolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Profile' => 'App\Policies\ProfilePolicy',

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
