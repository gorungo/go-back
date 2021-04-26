<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Action;
use App\Http\Resources\Action as ActionResource;
use Illuminate\Http\Request;
use App\Http\Requests\Action\StoreAction;
use App\Http\Requests\Photo\UploadPhoto;


class ActionController extends Controller
{

    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $categories = null)
    {

    }

    /**
     * Show the form for creating a new resource.
     * @param  Action $action
     * @return ActionResource
     */
    public function create(Action $action)
    {
        return new ActionResource($action->loadMissing(['actionPrice', 'actionIdea', 'actionPlaces', 'actionDates']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAction $request
     * @param  Action $action
     * @return ActionResource
     */
    public function store(StoreAction $request, Action $action)
    {
        return new ActionResource($action->createAndSync($request));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Action $action
     * @return ActionResource
     */
    public function edit(Action $action)
    {
        return new ActionResource($action->loadMissing(['actionPrice', 'actionIdea', 'actionPlaces', 'actionDates']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreAction $request
     * @param  Action $action
     * @return ActionResource
     */
    public function update(StoreAction $request, Action $action)
    {
        return new ActionResource($action->updateAndSync($request));
    }

    /**
     * Display the specified resource.
     *
     * @param Action $action
     * @return ActionResource
     */
    public function show(Action $action)
    {
        return new ActionResource($action->loadMissing(['actionPrice', 'actionIdea', 'actionPlaces', 'actionDates']));
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


    /**
     * Return list of items photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPhotosListJson(){
        return response()->json($this->action->ideaPhotos()->isActive()->get());
    }

    /**
     * Return list of items photo
     * @param UploadPhoto $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(UploadPhoto $request, $itemId){

        $action = Action::where('id', $itemId)->first();
        if($action) return response()->json($action->uploadPhoto($request));

        return response()->json(['type' => 'error', 'itemId' => $itemId]);
    }
}
