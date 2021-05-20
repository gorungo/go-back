<?php

namespace App\Models;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Requests\OSM\Store;
use App\Http\Resources\OSM as OSMResource;
use Exception;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class OSM extends Model
{
    use SpatialTrait;
    private $method = 'get';
    protected $table = 'osms';
    protected $fillable = [
        'boundingbox', 'icon', 'importance', 'lat', 'lon', 'licence', 'osm_id',
        'osm_type', 'place_id', 'type', 'coordinates'
    ];

    protected $spatialFields = [
        'coordinates',
    ];

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function ideas()
    {
        return $this->belongsToMany('App\Models\Idea', 'idea_place', 'place_id', 'idea_id');
    }

    public function localisedDescription()
    {
        return $this
            ->hasOne('App\Models\OSMDescription', 'place_id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    public function descriptions()
    {
        return $this->hasMany('App\Models\OSMDescription', 'place_id');
    }

    public function getTitleAttribute()
    {
        if ($this->localisedDescription != null) {
            return $this->localisedDescription->title;
        } else {
            if ($this->descriptions()->first()) {
                return $this->descriptions()->first()->title;
            }
        }

        return '';
    }

    public function getCountryTitleAttribute()
    {
        if ($this->localisedDescription != null) {
            return $this->localisedDescription->country_title;
        } else {
            if ($this->descriptions()->first()) {
                return $this->descriptions()->first()->country_title;
            }
        }

        return '';
    }

    public function getDisplayNameAttribute()
    {
        if ($this->localisedDescription != null) {
            return $this->localisedDescription->display_name;
        } else {
            if ($this->descriptions()->first()) {
                return $this->descriptions()->first()->display_name;
            }
        }

        return '';
    }

    private function execute($uri, $params = [])
    {
        $response = null;
        $client = new Client([
            'base_uri' => config('openstreetmap.base_url'),
            'cookies' => true,
        ]);

        try {
            if ($this->method === 'post') {
                $response = $client->post($uri, [
                    'json' => $params,
                ]);
            } elseif ($this->method === 'get') {
                $response = $client->get($uri, [
                    'query' => $params,
                ]);
            }
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        return $response ? json_decode($response->getBody()->getContents(), true) : null;
    }

    public function search(Request $request)
    {
        $externalSearch = [];
        $placeIds = [];
        $result = [];
        // делаем запрос в осм
        // сохраняем результат в нашу базу
        // делам запрос в нашу базу
        // отправляем результат

        if($request->has('q') && $request->q !== ''){
            $params = [
                'q' => $request->q,
                'format' => $request->get('format'),
                'accept-language' => $request->get('accept-language'),
            ];
            try {
                $externalSearch = $this->execute('search.php', $params);
            } catch (Exception $e) {
                //
            }

            $result = OSMResource::collection(OSM::findByTitle($request->q, $params))->collection->toArray();
        }

        foreach ($result as $res){
            $placeIds[] = $res['place_id'];
        }

        foreach ($externalSearch as $search){
            if(!in_array($search['place_id'], $placeIds)){
                $result[] = $search;
            }
        }

        return $result;
    }

    public static function findByTitle($q, $params)
    {
        return self::whereHas('descriptions', function($query) use ($q){
            return $query->where('display_name', 'like', '%'.$q.'%')->orWhere('title', 'like', '%'.$q.'%');
        })->orderBy('importance', 'desc')->get();

    }

    public static function createAndStore(Store $request)
    {
        $osm = DB::transaction(function () use ($request) {
            $omsData = $request->post();

            $nameExplode = explode(',', $omsData['display_name']);
            if(count($nameExplode) > 0){
                $title = $nameExplode[0];
                $countryTitle = $nameExplode[count($nameExplode) -1];
            }

            $omsDescriptionData = [
                'title' => $title ?? '',
                'country_title' => $countryTitle ?? '',
                'display_name' => $omsData['display_name'],
                'locale_id' => LocaleMiddleware::getLocaleId(),
                'place_id' => $omsData['place_id'],
            ];

            $omsData['coordinates'] = new Point($omsData['lat'], $omsData['lon']);
            $omsData['boundingbox'] = serialize($omsData['boundingbox']);

            $osm = OSM::create($omsData);
            $osm->localisedDescription()->create($omsDescriptionData);

            return $osm;

        });

        return $osm;
    }

    public function updateAndStore(Store $request)
    {
        $osm = DB::transaction(function () use ($request) {
            $omsData = $request->post();
            $omsDescriptionData = [
                'display_name' => $omsData['display_name'],
                'locale_id' => LocaleMiddleware::getLocaleId()
            ];

            if(!$this->localisedDescription){
                $this->localisedDescription()->create($omsDescriptionData);
            } else{
                $this->localisedDescription()->update($omsDescriptionData);
            }

            return $this;
        });

        return $osm;
    }

    public static function createFrom($data)
    {
        return DB::transaction(function () use ($data) {
            $osm = self::where('place_id', $data['place_id'])->first();

            if($data['place_id'] && !$osm){
                $descriptionData = [
                    'display_name' => $data['display_name'],
                    'locale_id' => LocaleMiddleware::getLocaleId(),
                    'place_id' => $data['place_id'],
                ];

                $data['coordinates'] = new Point($data['lat'], $data['lon']);
                $data['boundingbox'] = serialize($data['boundingbox']);

                $osm = self::create($data);
                $osm->localisedDescription()->create($descriptionData);
            }
            return $osm;
        });
    }
}
