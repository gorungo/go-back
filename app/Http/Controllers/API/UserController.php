<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Http\Requests\User\Store;
use App\Models\User;
use App\Http\Requests\User\SetNewPassword;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Idea as IdeaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Store  $request
     * @param  User  $user
     * @return UserResource
     */
    public function update(Store $request, User $user) : UserResource
    {
        return new UserResource($user->updateAndSync($request));
    }


    /**
     * Display a listing of the resource.
     * @param User $user
     * @return ResourceCollection
     */
    public function ideas(User $user)
    {
        return IdeaResource::collection($user
            ->ideas()
            ->joinDescription()
            ->get()
            );

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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function setNewPassword(SetNewPassword $request, User $user){
        $this->authorize('update', $user);
        $result = $user->setNewPassword($request);
        return response([
            'type' => $result,
            'message' => $result ? __('profile.password_set'):__('profile.password_not_set')
        ]);
    }
}
