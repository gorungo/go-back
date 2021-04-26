<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Idea;
use App\Http\Resources\Date as DateResource;
use App\Itinerary;
use App\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class IdeaDateController extends Controller
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
     * @return JsonResponse
     */
    public function store(Request $request, Idea $idea)
    {
        try {
            $date = DB::transaction(function () use ($request, $idea) {

                $r = $request->input('attributes');
                $p = $request->input('relationships.ideaPrice.attributes');

                $date = $idea->ideaDates()->create([
                    'start_date' => $r['start_date'],
                    'start_time' => $r['start_time'],
                    'time_zone_offset' => (int) $r['time_zone_offset'],
                ]);
                $date->ideaPrice()->create([
                    'idea_id' => $idea->id,
                    'idea_price_type_id' => 1,
                    'age_group_id' => 1,
                    'price' => (int) $p['price'],
                    'currency_id' => (int) $p['currency_id'],
                ]);
                return $date;
            });

            return response()->json(['data'=>new DateResource($date->refresh()->load(['ideaPrice']))]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

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
