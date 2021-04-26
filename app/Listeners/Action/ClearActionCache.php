<?php

namespace App\Listeners\Action;

use App\Events\Action\ActionUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearActionCache
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
     * @param  ActionUpdated  $event
     * @return void
     */
    public function handle(ActionUpdated $event)
    {
        //
    }
}
