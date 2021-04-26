<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Idea;

class TagController extends Controller
{
    public function allMainTagsCollection()
    {
        return Idea::allMainTagsCollectionCached();
    }
}
