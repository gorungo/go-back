<?php

namespace App\Http\Controllers\API\Photo;
use App\Http\Controllers\Controller;

use App\Profile;
use App\Photo;
use App\Http\Requests\Photo\SetMainPhoto;
use App\Http\Requests\Photo\UploadProfilePhoto;

class ProfileController extends Controller
{

    protected $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get photos list
     * @param Profile $profile
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Profile $profile)
    {
        return response()->json(['files' => $profile->photos()->get()]);
    }

    /**
     * Upload new photo
     * @param UploadProfilePhoto $request
     * @param Profile $profile
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(UploadProfilePhoto $request, Profile $profile)
    {
        return response()->json(['file' => $profile->uploadPhoto($request)]);
    }


    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Profile $profile
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(SetMainPhoto $request, Profile $profile)
    {

        if ($profile->deletePhoto()) {
            return response()->json(['type' => 'ok']);
        }

        return response()->json(['type' => 'error']);

    }

}
