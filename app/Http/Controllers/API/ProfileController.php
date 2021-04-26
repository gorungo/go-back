<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\User;
use App\Profile;
use App\Http\Resources\Profile as ProfileResource;
use Illuminate\Http\Request;
use App\Http\Requests\Profile\Store;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{

    protected $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $user)
    {
        return response()->json(ProfileResource($user->profile));
    }

    /**
     * Show the form for creating a new resource.
     * @param  Profile $profile
     * @return ProfileResource
     */
    public function create(Profile $profile)
    {
        return new ProfileResource($profile);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Store $request
     * @param  Profile $profile
     * @return ProfileResource
     */
    public function store(Store $request, Profile $profile)
    {
        return new ProfileResource($profile->createAndSync($request));
    }

    /**
     * Display the specified resource.
     *
     * @param Profile $profile
     * @return ProfileResource
     */
    public function show(Profile $profile)
    {
        return new ProfileResource($profile);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Profile $profile
     * @return ProfileResource
     */
    public function edit(Profile $profile)
    {
        return new ProfileResource($profile->load('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Store $request
     * @param  Profile $profile
     * @return ProfileResource
     */
    public function update(Store $request, Profile $profile)
    {
        return new ProfileResource($profile->updateAndSync($request));
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
        return response()->json($this->profile->profilePhotos()->isActive()->get());
    }


    /**
     * Return list of items photo
     * @param UploadPhoto $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(UploadPhoto $request, $itemId){

        $profile = Profile::where('id', $itemId)->first();
        if($profile) return response()->json($profile->uploadPhoto($request));

        return response()->json(['type' => 'error', 'itemId' => $itemId]);
    }

}
