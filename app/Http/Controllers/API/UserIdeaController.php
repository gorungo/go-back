<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests\Idea\PublishIdea;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Resources\Idea as IdeaResource;
use App\Idea;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserIdeaController extends Controller
{

    protected $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * Display a listing of the resource.
     * @param  Request  $request
     * @param  User  $user
     * @return AnonymousResourceCollection
     */
    public function index(Request $request, User $user)
    {
        return IdeaResource::collection(
            $user->ideas()->where(function ($q) use ($request) {
                if ($request->has('active')) {
                    $q->where('active', $request->active);
                }
                if ($request->has('approved')) {
                    $q->whereNotNull('approved_at');
                }
            })->paginate()->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',',
                    request()->include) : [])
        );
    }
}
