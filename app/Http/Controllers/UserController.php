<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\Profile as ProfileResource;

class UserController extends Controller
{
    public function show(Request $request, User $user)
    {
        $categories = null;
        $actions = $user->actions()->isActive()->paginate();
        return view('user.show' , compact(['user','actions','categories']));
    }

    public function showIdeas(Request $request, User $user)
    {
        $ideas = $user->ideas()->isActive()->get();
        return view('user.ideas' , compact(['user','ideas']));
    }
}
