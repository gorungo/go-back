<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Idea\PublishIdea;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Resources\Idea as IdeaResource;
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
        if ($request->has('sectionName')) {
            switch ($request->sectionName) {
                case "nearby":
                    return IdeaResource::collection(
                        Idea::widgetMainItemsList($request)
                    );
                    break;


                case "base":
                    return IdeaResource::collection(
                        Idea::widgetMainItemsList($request)
                    );
                    break;


                case "popular":
                    return IdeaResource::collection(
                        Idea::widgetMainItemsList($request)
                    );
                    break;

                default:
                    break;
            }
        }
        if ($request->has('q')) {
            switch ($request->q) {
                case 'not-moderated':
                    return IdeaResource::collection(Idea::notModerated()->take($request->limit)->get()->loadMissing([
                        'ideaPrice',
                        'ideaPlaces',
                        'ideaDates',
                        'ideaParentIdea',
                        'ideaCategories',
                        'ideaItineraries'
                    ]));

                default:
                    break;
            }
        }
        // listing
        return IdeaResource::collection(
            Idea::itemsList($request)
                ->loadMissing(request()->has('include') && request()->input('include') != '' ? explode(',',
                    request()->include) : [])
        );
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
     */
    public function updateRelationship(StoreIdea $request, Idea $idea, string $relationship): JsonResponse
    {
        $idea->updateRelationship($request, $relationship);
        return response()->json($relationship.' relationship updated', 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $hid
     * @return JsonResponse
     */
    public function destroy($idea)
    {
        $idea->delete();
        return response()->json('deleted', 200);
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
     */
    public function uploadPhoto(UploadPhoto $request, $itemId)
    {

        $idea = Idea::where('id', $itemId)->first();
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

    public function validateIdea(PublishIdea $request, Idea $idea)
    {
        return response()->json(['message' => 'ok'], 200);
    }
}
