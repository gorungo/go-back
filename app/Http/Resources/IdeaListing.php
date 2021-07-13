<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;

use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Date as DateResource;
use App\Http\Resources\Idea as IdeaResource;
use App\Http\Resources\Itinerary as ItineraryResource;
use App\Http\Resources\Photo as PhotoResource;
use App\Http\Resources\IdeaPrice as IdeaPriceResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\OSM as OsmResource;
use App\Http\Resources\Tagged as TaggedResource;
use Illuminate\Http\Resources\Json\JsonResource;

class IdeaListing extends JsonResource
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
            'type' => 'ideas',
            'hid' => $this->hid,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'title' => $this->id ? $this->title : '',
                'start_date' => $this->start_date,
                'image_url' => $this->imageUrl,
                'image2x_url' => $this->image2xUrl,
                'place_title' => $this->ideaPlace ? $this->ideaPlace->title : '',
                'author_hid' => $this->author->hid,
                'author_intro' => $this->author->profile->description,
                'author_image_url' => $this->author->imageUrl,
            ],
        ];
    }
}
