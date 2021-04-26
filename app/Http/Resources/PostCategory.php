<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PostCategory as PostCategoryResource;
class PostCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'postCategories',
            'id' => $this->id,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'active' => $this->active ? $this->active : 1,
                'order' => $this->order ? $this->order : 0,
                'title' => $this->title,
                'intro' => $this->intro,
                'description' => $this->description,
                'url' => $this->url,
                'edit_url' => $this->editUrl,
            ],

            'relationships' => [

                // селектор категорий может работать только с коллекцией,
                // поэтому приходится передавать главную категорию дважды
                // одну для селектора, другую, как настоящую
                'categories' => PostCategoryResource::collection($this->categoryParent()->get()),
                'categoryParent' => new PostCategoryResource($this->whenLoaded('categoryParent')),
            ],
        ];
    }
}
