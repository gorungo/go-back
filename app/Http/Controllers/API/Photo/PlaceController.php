<?php

namespace App\Http\Controllers\API\Photo;

use App\Place;
use App\Photo;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Requests\Photo\SetMainPhoto;
use App\Http\Controllers\Controller;

class PlaceController extends Controller
{

    protected $place;

    public function __construct(Place $place)
    {
        $this->place = $place;
    }

    /**
     * Get photos list
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Place $place)
    {
        return response()->json(['files' => $place->photos()->get()]);
    }

    /**
     * Upload new photo
     * @param UploadPhoto $request
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(UploadPhoto $request, Place $place)
    {
        return response()->json(['file' => $place->uploadPhoto($request)]);
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Place $place
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */

    public function setMain(SetMainPhoto $request, Place $place, Photo $photo)
    {
        return response()->json($photo->setMain());
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Place $place
     * @param Photo $photo
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(SetMainPhoto $request, Place $place, Photo $photo)
    {

        if ($photo->deletePhoto()) {
            $photo->delete();
            return response()->json(['type' => 'ok']);
        }

        return response()->json(['type' => 'error']);

    }

}
