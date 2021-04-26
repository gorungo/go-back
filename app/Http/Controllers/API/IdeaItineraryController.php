<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Idea;
use App\Http\Resources\Itinerary as ItineraryResource;
use App\Itinerary;
use App\Place;
use Illuminate\Http\Request;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class IdeaItineraryController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param  Request  $request
     * @param  Idea  $idea
     */
    public function index(Request $request, Idea $idea)
    {
       //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Idea  $idea
     * @return ItineraryResource
     */
    public function store(Request $request, Idea $idea)
    {
        $itinerary = DB::transaction(function () use ($request, $idea) {
            $r = $request->post('attributes');
            $descriptionStoreData = [
                'title' => $r['title'],
                'description' => $r['description'],
                'what_included' => $r['what_included'],
                'will_visit' => $r['will_visit'],
                'locale_id' => LocaleMiddleware::getLocaleId(),
            ];

            $itinerary = $idea->ideaItineraries()->create([
                'start_time' => $r['start_time'],
                'day_num' => $r['day_num'],
                'day_order' => $r['day_order'],
            ]);

            $itinerary->localisedItineraryDescription()->create($descriptionStoreData);
            return $itinerary;
        });

        return new ItineraryResource($itinerary->refresh());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreIdea  $request
     * @param  Idea  $idea
     * @param  Itinerary  $itinerary
     * @return ItineraryResource
     */
    public function update(StoreIdea $request, Idea $idea, Itinerary $itinerary)
    {
        return new ItineraryResource($itinerary->update($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Itinerary  $itinerary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Itinerary $itinerary)
    {
        //
    }

}
