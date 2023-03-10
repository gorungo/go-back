<?php

namespace App\Http\Controllers\API\Photo;

use App\Models\Idea;
use App\Models\Photo;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Requests\Photo\SetMainPhoto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Photo as PhotoResource;

class IdeaController extends Controller
{

    protected $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * Get photos list
     * @param Idea $idea
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Idea $idea)
    {
        return response()->json(['files' => $idea->photos()->get()]);
    }

    /**
     * Upload new photo
     * @param UploadPhoto $request
     * @param Idea $idea
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(UploadPhoto $request, Idea $idea)
    {
        return response()->json(['data' => new PhotoResource($idea->uploadPhoto($request))]);
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Idea $idea
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */

    public function setMain(SetMainPhoto $request, Idea $idea, Photo $photo)
    {
        return response()->json(['data'=>$photo->setMain()], 200);
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Idea $idea
     * @param Photo $photo
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(SetMainPhoto $request, Idea $idea, Photo $photo)
    {

        if ($photo->deletePhoto()) {
            $photo->delete();
            return response()->json(['type' => 'ok']);
        }

        return response()->json(['type' => 'error']);

    }

}
