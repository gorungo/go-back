<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category as CategoryResource;
class Category extends JsonResource
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
            'type' => 'categories',
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
                'categories' => CategoryResource::collection($this->categoryParent()->get()),
                'categoryParent' => new CategoryResource($this->categoryParent),
            ],
        ];
    }
}
