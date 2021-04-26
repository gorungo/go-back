<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Post;
use App\Http\Resources\Post as PostResource;
use Illuminate\Http\Request;
use App\Http\Requests\Post\StorePost;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $categories = null)
    {

    }

    /**
     * Show the form for creating a new resource.
     * @param  Post $post
     * @return PostResource
     */
    public function create(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePost $request
     * @param  Post $post
     * @return PostResource
     */
    public function store(StorePost $request, Post $post)
    {
        return new PostResource($post->createAndSync($request));
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return PostResource
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post $post
     * @return PostResource
     */
    public function edit(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StorePost $request
     * @param  Post $post
     * @return PostResource
     */
    public function update(StorePost $request, Post $post)
    {
        return new PostResource($post->updateAndSync($request));
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
        return response()->json($this->post->postPhotos()->isActive()->get());
    }

    public function getAllAvailableTags(){
        return $this->post->getAllTags();
    }


    /**
     * Return list of items photo
     * @param UploadPhoto $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(UploadPhoto $request, $itemId){

        $idea = Post::where('id', $itemId)->first();
        if($idea) return response()->json($idea->uploadPhoto($request));

        return response()->json(['type' => 'error', 'itemId' => $itemId]);
    }
}
