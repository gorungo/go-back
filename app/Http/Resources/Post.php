<?php

namespace App\Http\Resources;

use App\Post as PostModel;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Resources\PostCategory as PostCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
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
            'type' => 'posts',
            'id' => $this->id ? $this->id : null,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'url' => $this->url,
                'edit_url' => $this->editUrl,

                'slug' => $this->id ? $this->slug : '',
                'active' => $this->id ? $this->active : 1,
                'main_category_id' => $this->id ? $this->main_category_id : null,
                'title' => $this->id ? $this->title : '',
                'intro' => $this->id ? $this->intro : '',
                'description' => $this->id ? $this->description : '',
            ],

            'relationships' => [
                'categories' => $this->id ? PostCategoryResource::collection($this->ideaCategories) : [],
                'tags' => $this->id ? $this->getItemTags() : PostModel::emptyTagsArray(),
            ],
        ];
    }
}
