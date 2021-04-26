<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;

use App\Http\Resources\Filter;

class MainFilter extends Model
{

    public static function hasFiler($filter, $value)
    {
        if (request()->has($filter)) {
            foreach (explode('-', urldecode(request()->input($filter))) as $activeFilter) {
                if ($activeFilter == $value) return $value;
            };
        }
        return false;
    }

    public static function queryString()
    {
        $queryString = '';

        foreach(request()->all() as $key=>$val){
            if($queryString === ''){
                $queryString = '?' . $key . '=' . $val;
            }else{
                $queryString = $queryString . '&' . $key . '=' . $val;
            }
        }

        return $queryString;
    }

    public static function getFilterTitle($filter)
    {

        $title = '';
        $filtersInTitleCount = 0;

        if (request()->has($filter) && request()->input($filter) != null) {
            foreach (explode(',', urldecode(request()->input($filter))) as $value) {

                if (self::isRealFilter($filter, $value)) {
                    $title = $title . ($title == '' ? '' : ', ') .
                        __('menu.' . mb_strtolower($filter) . '_' . mb_strtolower($value));
                    $filtersInTitleCount++;
                }

                if ($filtersInTitleCount > 1) break;
            };
        } else {
            $title = __('menu.' . mb_strtolower($filter) . '_');
        }

        return $title;
    }

    private static function isRealFilter(String $filter, String $value)
    {

        $mainFilters = collect(config('menu.filters'));

        // if filter name exists
        if (!$mainFilters->has(mb_strtolower($filter))) return false;

        // if filter value exists
        $filterItems = collect($mainFilters->get($filter));

        if ($filterItems->contains(mb_strtolower($value))) {
            return true;
        }

        return false;

    }


    public static function getFiltersTagsArray()
    {
        $filtersTags = [];

        foreach (config('menu.filters') as $filter => $values) {
            if (request()->has($filter) && request()->input($filter) != '') {
                foreach ($values as $value) {
                    if (self::hasFiler($filter, $value)) {
                        $filtersTags[] = $value;
                    }
                }
            }

        }

        return $filtersTags;
    }

    /**
     * From which geo position we finding objects
     * @return Point
     */
    public static function searchPoint()
    {
        $placeMode = Place::placeMode();
        switch($placeMode){
            case 'place_position':
                if(request()->has('place_id')){
                    $osm = OSM::where('place_id', request()->place_id)->first();
                    if($osm){
                        return $osm->coordinates;
                    }
                }
                break;

            case 'place_id':
                return null;

            case 'nearby':
                return User::currentPosition();
        }

        // if do not have search_type query parameter but have place_id
        if(request()->has('place_id')){
            return null;
        }
        return User::currentPosition();

    }

    /**
     * In what radius we finding geo objects
     * @return Point
     */
    public static function searchDistance()
    {
        $distance = config('main_filter.distance.default');
        if (request()->has('distance') && in_array( request()->input('distance'), config('menu.filters.distance'))) {
            $distance = config('main_filter.distance')[request()->input('distance')];
        }

        return $distance;
    }

    /**
     * Filters collection to send to frontend
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function seasonFiltersArray()
    {
        return Filter::collection([
            'season_' => __('menu.season_'),
            'season_spring' => __('menu.season_spring'),
            'season_summer' => __('menu.season_summer'),
            'season_autumn' => __('menu.season_autumn'),
            'season_winter' => __('menu.season_winter'),
        ]);

    }

    /**
     * Filters collection to send to frontend
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function timeFiltersArray()
    {
        return Filter::collection([
            'daytime_' => __('menu.daytime_'),
            'daytime_morning' => __('menu.daytime_morning'),
            'daytime_day' => __('menu.daytime_day'),
            'daytime_evening' => __('menu.daytime_evening'),
            'daytime_night' => __('menu.daytime_night'),
        ]);

    }
}
