<?php

namespace App\Http\Controllers\API\Photo;

use App\Idea;
use App\Itinerary;
use App\Photo;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Requests\Photo\DestroyPhoto;
use App\Http\Requests\Photo\SetMainPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItineraryController extends Controller
{
    protected $itinerary;

    public function __construct(Itinerary $itinerary)
    {
        $this->itinerary = $itinerary;
    }

    /**
     * Get photos list
     * @param Itinerary $itinerary
     * @return JsonResponse
     */

    public function index(Itinerary $itinerary) : JsonResponse
    {
        return response()->json(['files' => $itinerary->photos()->get()]);
    }

    /**
     * Upload new photo
     * @param UploadPhoto $request
     * @param Itinerary $itinerary
     * @return JsonResponse
     */

    public function uploadMain(UploadPhoto $request, Itinerary $itinerary) : JsonResponse
    {
        return response()->json(['file' => $itinerary->uploadPhoto($request)]);
    }

    /**
     * Upload new photo
     * @param UploadPhoto $request
     * @param Itinerary $itinerary
     * @return JsonResponse
     */

    public function upload(UploadPhoto $request, Itinerary $itinerary) : JsonResponse
    {
        return response()->json(['file' => $itinerary->uploadPhoto($request)]);
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Itinerary $itinerary
     * @param Photo $photo
     * @return JsonResponse
     */

    public function setMain(SetMainPhoto $request, Itinerary $itinerary, Photo $photo) : JsonResponse
    {
        return response()->json($photo->setMain());
    }

    /**
     * Set image as main
     * @param DestroyPhoto $request
     * @param Itinerary $itinerary
     * @param Photo $photo
     * @throws \Exception
     * @return JsonResponse
     */

    public function destroy(DestroyPhoto $request, Itinerary $itinerary, Photo $photo) : JsonResponse
    {

        if ($photo->deletePhoto()) {
            $photo->delete();
            return response()->json(['message' => 'ok']);
        }

        return response()->json(['message' => 'error']);

    }

}
