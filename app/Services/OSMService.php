<?php


namespace App\Services;

use App\Models\Category;
use App\Models\Idea;
use App\Models\OSM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class OSMService
{
    public static function popular()
    {
        return Cache::tags(['osm'])->remember('osm_'.request()->getQueryString(),
            10, function () {
                $popularOsmIds = [7,56,55];
                return OSM::whereIn('id', $popularOsmIds)->get();
            });
    }
}
