<?php

namespace App\Http\Controllers\API\Photo;

use App\Action;
use App\Photo;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Requests\Photo\SetMainPhoto;
use App\Http\Controllers\Controller;

class ActionController extends Controller
{

    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * Get photos list
     * @param Action $action
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Action $action)
    {
        return response()->json(['files' => $action->photos()->get()]);
    }

    /**
     * Upload new photo
     * @param UploadPhoto $request
     * @param Action $action
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(UploadPhoto $request, Action $action)
    {
        return response()->json(['file' => $action->uploadPhoto($request)]);
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Action $action
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */

    public function setMain(SetMainPhoto $request, Action $action, Photo $photo)
    {
        return response()->json($photo->setMain());
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Action $action
     * @param Photo $photo
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(SetMainPhoto $request, Action $action, Photo $photo)
    {

        if ($photo->deletePhoto()) {
            $photo->delete();
            return response()->json(['type' => 'ok']);
        }

        return response()->json(['type' => 'error']);

    }

}
