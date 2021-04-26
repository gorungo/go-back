<?php

namespace App\Http\Controllers;

use App\Page;
use App\Place;
use App\PlaceType;
use App\PlaceDescription;

use App\Http\Requests\Photo\UploadPhoto;
use Illuminate\Http\Request;
use App\Http\Requests\Filter\GetFilterItems;

use App\Http\Resources\PlaceNoRelationships;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{

    protected $place;

    public function __construct(Place $place)
    {
        $this->idea = $place;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param PlaceType $placeType
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ?PlaceType $placeType)
    {
        $page = new Page();
        $page->title = config('app.name') . ' - ' . __('place.description') . '.';

        $activePlace = Place::activePlace();
        $sectionTitle = __('place.description');

        if($activePlace){
            $sectionTitle =__('place.places_close_to') .' '. $activePlace->title;
        }


        return view('place.index', [
            'page' => $page,
            'places' => Place::itemsList($request),
            'sectionTitle' => $sectionTitle,
            'activePlace' => $activePlace,
            'activePlaceResource' => $activePlace ? new PlaceNoRelationships($activePlace) : null,
            'activePlaceType' => $placeType,
            'placeTypes' => PlaceType::widgetActivePlaceTypes(),
            'backgroundImage' => Place::backgroundImage(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Place $place)
    {
        return view('place.edit' , compact(['place']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  GetFilterItems $request
     * @param  Place $place
     * @return \Illuminate\Http\Response
     */
    public function store(GetFilterItems $request, Place $place)
    {
        $place = $place->createAndSync($request);

        if($place){
            return redirect()->route('ideas.edit', $place->id )->with('status', __('place.created'));

        }else{
            return redirect()->back()->withInput()->with('status', __('idea.not_created'));

        }
    }


    /**
     * @param Place $place
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function show(Place $place)
    {

        $item = $place;
        $placeActions = $place->actions;

        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Новый товар',  'url' => '#'],
        ];*/

        return view('place.show' , compact(['item', 'category', 'placeActions', 'breadcrumb_array' ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Place $place
     * @return \Illuminate\Http\Response
     */
    public function edit(Place $place)
    {
        return view('place.edit',  compact(['place']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  GetFilterItems $request
     * @param  Place $place
     * @return \Illuminate\Http\Response
     */
    public function update(GetFilterItems $request, Place $place)
    {

        $updateResult = $place->updateAndSync($request);

        if($updateResult){
            // очищаем старый кэш
            //Cache::forget('category-all');
            //Cache::forget('category-widget');

            return redirect()->back()->with('status', __('idea.updated'));
        }else{
            return redirect()->back()->with('status', __('idea.not_updated'));

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
