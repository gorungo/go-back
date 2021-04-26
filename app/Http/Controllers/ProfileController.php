<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use Illuminate\Http\Request;
use App\Http\Resources\Profile as ProfileResource;

class ProfileController extends Controller
{
    public function edit(Request $request, User $user)
    {
        $userProfile = $user->profile;

        if(!$userProfile){
            $userProfile = Profile::createFor($user);
        }

        return view('profile.edit' ,[
            'user' => $user,
            'userProfile' => $userProfile,
            'profileResource' => new ProfileResource($userProfile)
        ]);
    }
}
