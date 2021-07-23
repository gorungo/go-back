<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Requests\OSM\Store;
use App\Http\Resources\OSM as OSMResource;
use App\Models\OSM;
use App\Services\OSMService;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class OSMController extends Controller
{
    protected $osm;
    protected $OSMService;

    public function __construct(OSM $osm)
    {
        $this->osm = $osm;
    }

    public function index(Request $request)
    {
        return response()->json(OSMService::popular());
    }

    public function search(Request $request)
    {
        return response()->json($this->osm->search($request));
    }

    public function show(Request $request, OSM $osm)
    {
        return response()->json(new OSMResource($osm));
    }

    public function view(Request $request, OSM $osm)
    {
        return response()->json(new OSMResource($osm));
    }

    public function saveSelected(Store $request)
    {
        // сохраняем новое место если нет
        // обновляем описание места если нет в текущей локали
        // ничего не делаем если не нужно ничего обновлять

        if (!$request->id && !OSM::where('place_id', $request->place_id)->first()){
            $place = OSM::createAndStore($request);
            return new OSMResource($place);
        } else {
            $place = OSM::where('place_id', $request->place_id)->first();
            if($place){
                $place->updateAndStore($request);
                return new OSMResource($place->refresh());
            }else{
                return response()->json('Already exists, not modified', 200);
            }

        }
    }
}
