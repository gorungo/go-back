<?php

namespace App\Listeners\Idea;

use App\Events\Idea\IdeaUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearIdeaCache
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
     * @param  IdeaUpdated  $event
     * @return void
     */
    public function handle(IdeaUpdated $event)
    {
        //
    }
}
