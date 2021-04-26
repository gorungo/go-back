<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\User;
use App\Profile;
use App\Http\Resources\Profile as ProfileResource;


class UserProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return ProfileResource
     */
    public function show(User $user)
    {
        return new ProfileResource($user->profile->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',', request()->include) : []));
    }

}
