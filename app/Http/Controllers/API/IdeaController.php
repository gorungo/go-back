<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Idea\PublishIdea;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Resources\Idea as IdeaResource;
use App\Http\Resources\IdeaListing as IdeaSimpleResource;
use App\Models\Idea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App;

class IdeaController extends Controller
{

    protected $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
        $this->middleware('auth')->except(['index', 'show']);
        $this->authorizeResource(Idea::class, 'idea');
    }

    /**
     * Display a listing of the resource.
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $ideas = null;

        if ($request->has('section_name')) {
            switch ($request->section_name) {
                case "nearby":
                    $ideas = App\Services\IdeaService::widgetMainItemsList($request, $request->limit);
                    break;

                default:
                    $ideas = App\Services\IdeaService::widgetMainItemsList($request, $request->limit);
                    break;

            }
        }
        else if ($request->has('q')) {
            switch ($request->q) {
                case 'not-moderated':
                    $ideas = Idea::notModerated()->take($request->limit)->get()->loadMissing([
                        'ideaPrice',
                        'ideaPlaces',
                        'ideaDates',
                        'ideaParentIdea',
                        'ideaCategories',
                        'ideaItineraries'
                    ]);
                    break;

                default:
                    break;
            }
        }
        else{
            // base listing
            $ideas = App\Services\IdeaService::itemsList($request)->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',',
                request()->include) : []);
        }


        if(request()->has('simple_resource') && request()->input('simple_resource') == '1')
        {
            return IdeaSimpleResource::collection($ideas);
        }

        return IdeaResource::collection($ideas);
    }

    /**
     * Show the form for creating a new resource.
     * @param  Idea  $idea
     * @return IdeaResource
     */
    public function create(Idea $idea)
    {
        return new IdeaResource($idea->loadMissing([
            'ideaPrice',
            'ideaPlaces',
            'ideaDates',
            'ideaParentIdea',
            'ideaCategories',
            'ideaItineraries'
        ]));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreIdea  $request
     * @param  Idea  $idea
     * @return IdeaResource
     */
    public function store(StoreIdea $request, Idea $idea)
    {
        return new IdeaResource($idea->createAndSync($request)->loadMissing([
            'ideaPrice',
            'ideaPlaces',
            'ideaDates',
            'ideaParentIdea',
            'ideaCategories',
            'ideaItineraries'
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  Idea  $idea
     * @return IdeaResource
     */
    public function show(Idea $idea)
    {
        if(request()->has('edit') && request()->input('edit') === '1'){
            return new IdeaResource($idea->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',',
                request()->include) : []));
        }
        return new IdeaResource($idea->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',',
            request()->include) : []));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Idea  $idea
     * @return IdeaResource
     */
    public function edit(Idea $idea)
    {
        return new IdeaResource($idea->loadMissing([
            'ideaPrice',
            'ideaPlaces',
            'ideaDates',
            'ideaParentIdea',
            'ideaCategories',
            'ideaItineraries'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreIdea  $request
     * @param  Idea  $idea
     * @return IdeaResource
     */
    public function update(StoreIdea $request, Idea $idea)
    {
        return new IdeaResource($idea->updateAndSync($request)
            ->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',', request()->include) : []));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreIdea  $request
     * @param  Idea  $idea
     * @param  string  $relationship
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateRelationship(StoreIdea $request, Idea $idea, string $relationship): JsonResponse
    {
        $this->authorize('update', $idea);
        $idea->updateRelationship($request, $relationship);
        return response()->json($relationship.' relationship updated', 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $idea
     * @return JsonResponse
     */
    public function destroy($idea)
    {
        $idea->delete();
        return response()->json(['message'=>'deleted'], 200);
    }


    /**
     * Return list of items photo
     * @return JsonResponse
     */
    public function getPhotosListJson()
    {
        return response()->json($this->idea->ideaPhotos()->isActive()->get());
    }

    /**
     * Return list of items photo
     * @param  UploadPhoto  $request
     * @param $itemId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function uploadPhoto(UploadPhoto $request, $itemId)
    {
        $idea = Idea::where('id', $itemId)->first();
        $this->authorize('update', $idea);
        if ($idea) {
            return response()->json($idea->uploadPhoto($request));
        }

        return response()->json(['type' => 'error', 'itemId' => $itemId]);
    }

    public function randomIdea()
    {
        return new IdeaResource(Idea::randomIdea());
    }

    public function getByTitle(Request $request)
    {
        return IdeaResource::collection(Idea::getByTitle($request->title));
    }

    public function getMain(Request $request)
    {
        return IdeaResource::collection(Idea::getMain($request->title));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function validateIdea(PublishIdea $request, Idea $idea): JsonResponse
    {
        $this->authorize('update', $idea);
        return response()->json(['message' => 'ok'], 200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function publish(PublishIdea $request, Idea $idea): JsonResponse
    {
        $this->authorize('update', $idea);
        $idea->publish();
        return response()->json(['message' => 'published'], 200);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function unPublish(Request $request, Idea $idea): JsonResponse
    {
        $this->authorize('update', $idea);
        $idea->unPublish();
        return response()->json(['message' => 'unpublished'], 200);
    }

}
