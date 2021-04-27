<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Idea;

class TagController extends Controller
{
    public function allMainTagsCollection()
    {
        return Idea::allMainTagsCollectionCached();
    }
}
