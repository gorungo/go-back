<?php

namespace App\Http\Controllers\API\Photo;

use App\Post;
use App\Photo;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Requests\Photo\SetMainPhoto;
use App\Http\Controllers\Controller;

class PostController extends Controller
{

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get photos list
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Post $post)
    {
        return response()->json(['files' => $post->photos()->get()]);
    }

    /**
     * Upload new photo
     * @param UploadPhoto $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(UploadPhoto $request, Post $post)
    {
        return response()->json(['file' => $post->uploadPhoto($request)]);
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Post $post
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */

    public function setMain(SetMainPhoto $request, Post $post, Photo $photo)
    {
        return response()->json($photo->setMain());
    }

    /**
     * Set image as main
     * @param SetMainPhoto $request
     * @param Post $post
     * @param Photo $photo
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(SetMainPhoto $request, Post $post, Photo $photo)
    {

        if ($photo->deletePhoto()) {
            $photo->delete();
            return response()->json(['type' => 'ok']);
        }

        return response()->json(['type' => 'error']);

    }

}
