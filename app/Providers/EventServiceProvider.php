<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Registered' => [
            'Illuminate\Auth\Listeners\SendEmailVerificationNotification',
            'App\Listeners\SetDefaultUserRole',
            'App\Listeners\CreateDefaultUserProfile',
        ],

        'App\Events\Action\ActionCreated' => [
            'App\Listeners\Action\ClearActionCache',
        ],
        'App\Events\Action\ActionUpdated' => [
            'App\Listeners\Action\ClearActionCache',
        ],

        'App\Events\Idea\IdeaCreated' => [
            'App\Listeners\Idea\ClearIdeaCache',
        ],
        'App\Events\Idea\IdeaUpdated' => [
            'App\Listeners\Idea\ClearIdeaCache',
        ],

        'App\Events\Place\PlaceCreated' => [
            'App\Listeners\Place\ClearPlaceCache',
        ],
        'App\Events\Place\PlaceUpdated' => [
            'App\Listeners\Place\ClearPlaceCache',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
