<?php

namespace App\Http\Resources;

use App\Idea;
use App\Http\Resources\Idea as IdeaResource;
use App\Http\Resources\ActionPrice as ActionPriceResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Place as PlaceResource;
use App\Http\Resources\Date as DateResource;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;

class Action extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $idea = null;

        if(!$this->id && request()->has('idea_id')){
            $idea = Idea::find(request()->input('idea_id'));
        }
        if(!$idea){
            $idea = $this->whenLoaded('actionIdea');
        }


        return [
            'type' => 'Actions',
            'id' => $this->id ? $this->id : null,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'url' => $this->url,
                'edit_url' => $this->editUrl,

                'slug' => $this->slug,
                'active' => $this->active,
                'main_category_id' => $this->main_category_id ,

                // localized information
                'title' => $this->title,
                'intro' => $this->intro,
                'description' => $this->description,
            ],

            'relationships' => [
                'idea' => new IdeaResource($idea),
                'author' => new UserResource($this->author),
                'price' => new ActionPriceResource($this->whenLoaded('actionPrice')),
                'places' => PlaceResource::collection($this->whenLoaded('actionPlaces')),
                'dates' => DateResource::collection($this->whenLoaded('actionDates'))
            ],
        ];
    }
}
