<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;


class SetDefaultUserRole
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle( $event )
    {
        Log::info('Default role ' . config('permission.default_role').' added to user ' . $event->user->id);
        $event->user->assignRole(config('permission.default_role'));
    }
}
