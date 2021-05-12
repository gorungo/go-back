<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests\Idea\PublishIdea;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Resources\Idea as IdeaResource;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

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
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, User $user)
    {
        $this->authorize('update', $user);

        return response()->json([ 'data' => IdeaResource::collection(
            $user->ideas()->where(function ($q) use ($request) {
                if ($request->has('active')) {
                    $q->where('active', $request->active);
                }
                if ($request->has('approved')) {
                    $q->whereNotNull('approved_at');
                }
            })->paginate()->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',',
                request()->include) : [])
        )], 200);
    }

    /**
     * Show the form for creating a new resource.
     * @param  User  $user
     * @return IdeaResource
     */
    public function createAndGetEmptyIdea(User $user): IdeaResource
    {
        return new IdeaResource(Idea::createEmptyOfUser($user)->loadMissing([
            'ideaPrice',
            'ideaPlace',
            'ideaPlacesToVisit',
            'ideaDates',
            'ideaCategories',
            'ideaItineraries'
        ]));
    }
}
