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
                'slug' => $this->slug,
                'parent_id' => $this->parent_id,
            ],

            'relationships' => [],
        ];
    }
}
