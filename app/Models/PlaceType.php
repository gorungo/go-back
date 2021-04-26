<?php

namespace App\Models;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlaceType extends Model
{
    protected $table = 'place_types';
    public $timestamps = false;

    public $guarded = [];

    protected $with = ['placeTypeDescriptions', 'localisedPlaceTypeDescription'];

    Public function getTitleAttribute()
    {

        if($this->localisedPlaceTypeDescription != null){
            return $this->localisedPlaceTypeDescription->title;
        }else if($this->placeTypeDescriptions()->first()){
            return $this->placeTypeDescriptions()->first()->title;
        }

        return '';
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        if (request()->is('api/*')) {
            return 'id';
        } else {
            return 'slug';
        }
    }

    public function placeTypeDescriptions()
    {
        return $this->hasMany('App\Models\PlaceDescription', 'place_id', 'id');
    }

    public function localisedPlaceTypeDescription()
    {
        return $this
            ->hasOne('App\Models\PlaceTypeDescription', 'place_type_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    public static function widgetActivePlaceTypes()
    {
        return Cache::tags(['placeType', 'widgetActivePlaceTypes'])
            ->remember('widgetActivePlaceTypes-' . LocaleMiddleware::getLocaleId(), 1, function () {

                return self::JoinDescription()
                    ->IsActive()
                    ->get();

            });
    }

    public function scopeJoinDescription($query)
    {
        return $query->join('place_type_descriptions', function ($join) {
            $join->on('place_types.id', '=', 'place_type_descriptions.place_type_id')
                ->where('locale_id', LocaleMiddleware::getLocaleId());
        })->select('place_types.*', 'place_type_descriptions.title');
    }

    public function scopeIsActive($query)
    {
        return $query;
    }

    /**
     * Array of region or city place type ids
     * @return array
     */
    public static function regionOrCityPlaceTypeIds()
    {
        return [1, 26];
    }
}
