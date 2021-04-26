<?php

namespace App\Models;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Requests\Place\StorePlace;
use App\Models\Traits\Hashable;
use App\Models\Traits\Imageble;
use DB;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;

class Place extends Model
{
    use SoftDeletes, SpatialTrait, Imageble, Sortable, Hashable;

    const hidLength = 20;
    public $timestamps = false;
    public $sortable = [
        'updated_at',
        'rating',
        'distance',
        'title'
    ];
    protected $table = 'places';
    protected $perPage = 60;
    protected $fillable = ['place_type_id', 'coordinates'];
    protected $with = ['localisedPlaceDescription', 'placeDescriptions'];
    protected $spatialFields = [
        'coordinates',
    ];

    public static function getByTitle(string $title)
    {
        return self::whereHas('placeDescriptions', function ($query) use ($title) {
            $query->where('title', 'like', '%'.$title.'%');
        })->take(20)->get();
    }

    public static function getRegionOrCityByTitle(string $title)
    {
        return self::RegionOrCity()->whereHas('placeDescriptions', function ($query) use ($title) {
            $query->where('title', 'like', '%'.$title.'%');
        })->take(20)->get();
    }

    public static function itemsList(Request $request)
    {
        return Cache::remember('places_'.LocaleMiddleware::getLocale().'_page_'.request()->page.'_distance_'.request()->distance,
            0, function () {
                return self::orderByPlace()
                    ->joinDescription()
                    ->search()
                    ->sortable()
                    ->paginate();
            });
    }

    public static function backgroundImage()
    {
        return '/images/bg/mountains_blue.svg';
    }

    public static function coordinatesById($id)
    {
        $place = self::whereId((int) $id)->isActive()->first();

        if ($place) {
            return $place->coordinates;
        }
        return null;
    }

    /**
     * Get currently viewing place ( if isset pl in request )
     * @return mixed
     *
     */
    public static function activePlace()
    {
        $place = null;
        if(self::placeMode() === 'place'){
            if (session()->has('current_place')) {

                $placeArr = session()->get('current_place');

                if(array_has($placeArr, ['data'])){
                    $place = $placeArr['data'];
                    if ($place->hid === request()->input('pl')) {
                        return $place;
                    }
                }
            }

            $place = Place::findByHid(request()->input('pl'));
            if ($place) {
                session()->put('current_place', [
                    'mode' => 'place',
                    'data' => $place,
                ]);
            }
        }


        return $place;
    }

    public static function placeMode()
    {
        $defaultPlaceMode = 'nearby';
        if (request()->has('search_type') && request()->input('search_type') !== ''){
            return request()->input('search_type');
        }
        return $defaultPlaceMode;
    }

    /**
     * Get last created place of authorized user
     */
    public static function lastCreatedPlaceOfAuthUser()
    {
        if (Auth()->User()) {
            return self::orderBy('id', 'desc')->first();
        }

        return null;
    }

    public function getHidAttribute()
    {
        return $this->getRouteKey() ?? null;
    }

    public function placeType()
    {
        return $this->belongsTo('App\Models\PlaceType', 'place_type_id');
    }

    public function osm()
    {
        return $this->hasOne('App\Models\Osm');
    }

    public function placeActions()
    {
        return $this->belongsToMany('App\Models\Action', 'action_place');
    }

    public function getUrlAttribute()
    {
        return route('places.show', [$this]);
    }

    public function getFullUrlAttribute()
    {
        return $this->url.MainFilter::queryString();
    }

    public function getEditUrlAttribute()
    {
        return route('places.edit', $this);
    }

    public function getTitleAttribute()
    {
        if ($this->localisedPlaceDescription != null) {
            return $this->localisedPlaceDescription->title;
        } else {
            if ($this->placeDescriptions()->first()) {
                return $this->placeDescriptions()->first()->title;
            }
        }

        return '';
    }

    public function placeDescriptions()
    {
        return $this->hasMany('App\Models\PlaceDescription', 'place_id', 'id');
    }

    public function getIntroAttribute()
    {
        if ($this->localisedPlaceDescription != null) {
            return $this->localisedPlaceDescription->intro;
        } else {
            if ($this->placeDescriptions()->first()) {
                return $this->placeDescriptions()->first()->intro;
            }
        }

        return '';
    }

    public function getDescriptionAttribute()
    {
        if ($this->placeDescriptions()->count()) {
            if ($this->localisedPlaceDescription != null) {
                return $this->localisedPlaceDescription->description;
            } else {
                if ($this->placeDescriptions()->first()) {
                    return $this->placeDescriptions()->first()->description;
                }
            }

        }
        return '';
    }

    /**
     * Get path to tmb img of category item
     * @return string
     */
    public function getTmbImgPathAttribute2()
    {
        $defaultTmb = 'images/interface/placeholders/location.png';
        return $defaultTmb;
    }

    /**
     * Get path to tmb img of category item
     * @return string
     */
    public function getTmbImgPathAttribute()
    {

        $defaultTmb = 'images/interface/placeholders/idea.png';

        if ($this->thmb_file_name != null) {
            $src = 'storage/images/place/'.$this->id.'/'.htmlspecialchars(strip_tags($this->thmb_file_name));
        } else {
            $src = $defaultTmb;
        }

        if (!file_exists($src)) {
            $src = $defaultTmb;
        }

        return $src;
    }

    public function createAndSync(StorePlace $request)
    {

        $createResult = DB::transaction(function () use ($request) {

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'place_type_id' => $request->input('relationships.placeType.id'),
                'coordinates' => new Point(
                    $request->input('attributes.coordinates.coordinates')[1],
                    $request->input('attributes.coordinates.coordinates')[0]
                ),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $place = self::create($storeData);
            $place->localisedPlaceDescription()->create($descriptionStoreData);

            $place->updateRelationships($request);

            return $place;

        });

        return $createResult;
    }

    public function createFromOSM($osm)
    {
        $createResult = DB::transaction(function () use ($osm) {

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'coordinates' => new Point(
                    $osm['lat'],
                    $osm['lon']
                ),
            ];

            $nameArray = explode(',', $osm['display_name']);

            $descriptionStoreData = [
                'title' => count($nameArray) > 0 ? $nameArray[0] : '',
                'intro' => $osm['display_name'],
                'description' => $osm['display_name'],
                'locale_id' => $localeId,
            ];

            $place = self::create($storeData);
            $place->localisedPlaceDescription()->create($descriptionStoreData);

            return $place;

        });

        return $createResult;
    }

    public function updateAndSync(StorePlace $request)
    {

        $updateResult = DB::transaction(function () use ($request) {

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'place_type_id' => $request->input('relationships.placeType.id'),
                'coordinates' => new Point(
                    $request->input('attributes.coordinates.coordinates')[1],
                    $request->input('attributes.coordinates.coordinates')[0]),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $this->update($storeData);
            if ($this->localisedPlaceDescription) {
                $this->localisedPlaceDescription()->update($descriptionStoreData);
            } else {
                $this->localisedPlaceDescription()->create($descriptionStoreData);
            }

            $this->updateRelationships($request);

            return $this;

        });


        return $updateResult;

    }

    public function localisedPlaceDescription()
    {
        return $this
            ->hasOne('App\Models\PlaceDescription', 'place_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    private function updateRelationships(StorePlace $request): void
    {
        $this->saveAddress($request);
    }

    private function saveAddress($request)
    {

        $localeId = LocaleMiddleware::getLocaleId();
        $address['country_code'] = 'RU';
        //$address['country_code'] = $request->input('relationships.address.attributes.country_code');

        $address['postal_code'] = $request->input('relationships.address.attributes.postal_code');


        $addressDescription['locale_id'] = $request->input('relationships.address.locale_id') !== null ? $request->input('relationships.address.locale_id') : $localeId;
        $addressDescription['address'] = $request->input('relationships.address.attributes.address');
        $addressDescription['region'] = $request->input('relationships.address.attributes.region');
        $addressDescription['city'] = $request->input('relationships.address.attributes.city');
        $addressDescription['country'] = $request->input('relationships.address.attributes.country');

        if ($request->input('relationships.address.id')) {
            $address = $this
                ->placeAddress()
                ->where('id', (int) $request->input('relationships.address.id'))
                ->update($address);

            if ($this->placeAddress->localisedAddressDescription) {
                $this->placeAddress->localisedAddressDescription()->update($addressDescription);
            } else {
                $this->placeAddress->localisedAddressDescription()->create($addressDescription);
            }


        } else {


            $address = $this
                ->placeAddress()
                ->create($address);

            $address
                ->localisedAddressDescription()
                ->create($addressDescription);
        }

    }

    public function placeAddress()
    {
        return $this->hasOne('App\Models\Address');
    }

    public function scopeJoinDescription($query)
    {
        return $query->join('place_descriptions', function ($join) {
            $join->on('places.id', '=', 'place_descriptions.place_id')
                ->where('locale_id', LocaleMiddleware::getLocaleId());
        })->select('places.*', 'place_descriptions.title', 'place_descriptions.intro');
    }

    public function scopeIsActive($query)
    {
        return $query;
    }

    public function scopeSearch($query)
    {
        if (request()->has('q')) {
            return $query->whereHas('placeDescriptions', function ($query) {
                $query->where('title', 'like', '%'.request()->q.'%');
            });
        }
        return $query;
    }

    public function distanceSortable($query, $direction)
    {
        return $query->orderByDistance('coordinates', MainFilter::searchPoint(), $direction);
    }

    public function titleSortable($query, $direction)
    {
        return $query->orderBy('place_descriptions.title', $direction);
    }

    public function ratingSortable($query, $direction)
    {
        return $query->orderBy('place_descriptions.title', $direction);
    }

    public function scopeRegionOrCity($query)
    {
        $regionOrCityPlaceTypeIds = PlaceType::regionOrCityPlaceTypeIds();
        return $query->whereIn('place_type_id', $regionOrCityPlaceTypeIds);
    }

    public function scopeNotRegionOrCity($query)
    {
        $regionOrCityPlaceTypeIds = PlaceType::regionOrCityPlaceTypeIds();
        return $query->whereNotIn('place_type_id', $regionOrCityPlaceTypeIds);
    }

    public function scopeOrderByPlace($query)
    {
        return $query
            ->distance('coordinates', MainFilter::searchPoint(), MainFilter::searchDistance())
            ->orderByDistance('coordinates', MainFilter::searchPoint(), 'asc');
    }


}
