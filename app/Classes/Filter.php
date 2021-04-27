<?php

namespace App\Classes;

use App\Http\Resources\Filter as FilterResource;

class Filter
{
    public $name = '';
    public $items = [];

    /**
     * Filters collection to send to frontend
     */
    public function resourceCollection()
    {
        return FilterResource::collection(collect($this->items));
    }
}
