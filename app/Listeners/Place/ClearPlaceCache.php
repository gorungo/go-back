<?php

namespace App\Listeners\Place;

use App\Events\Place\PlaceUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearPlaceCache
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
     * @param  PlaceUpdated  $event
     * @return void
     */
    public function handle(PlaceUpdated $event)
    {
        //
    }
}
