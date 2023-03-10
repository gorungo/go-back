<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;

use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Date as DateResource;
use App\Http\Resources\Idea as IdeaResource;
use App\Http\Resources\Itinerary as ItineraryResource;
use App\Http\Resources\Place as PlaceResource;
use App\Http\Resources\Photo as PhotoResource;
use App\Http\Resources\IdeaPrice as IdeaPriceResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\OSM as OsmResource;
use App\Http\Resources\Tagged as TaggedResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Idea extends JsonResource
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
                'slug' => $this->id ? $this->slug : '',
                'active' => $this->id ? $this->active : 1,
                'is_approved' => (bool) $this->approved_at,
                'is_published' => $this->isPublished,
                'title' => $this->id ? $this->title : '',
                'intro' => $this->id ? $this->intro : '',
                'author_hid' => $this->author->hid,
                'description' => $this->id ? $this->description : '',
                'date_from' => $this->date_from,
                'image_url' => $this->imageUrl,
                'image2x_url' => $this->image2xUrl,
                'place_id' => $this->place_id,
                'idea_type_id' => $this->idea_type_id,
                'place_title' => $this->ideaPlace ? $this->ideaPlace->title : '',
                'country_title' => $this->ideaPlace ? $this->ideaPlace->country_title : '',
                'options' => json_decode($this->options),
                'created_at' => $this->created_at ? (string)$this->created_at : null,

                'author_intro' => $this->author->profile->description,

                'booking_info' => $this->author->profile->bookingInfo ? $this->author->profile->bookingInfo->info : '',
                'booking_contacts' => $this->author->profile->bookingInfo ? $this->author->profile->bookingInfo->contacts : '',
            ],

            'relationships' => [
                'idea' => new IdeaResource($this->whenLoaded('ideaParentIdea')),
                'photos' => PhotoResource::collection($this->whenLoaded('photos')),
                'categories' => $this->id ? CategoryResource::collection($this->whenLoaded('ideaCategories')) : [],
                'author' => new UserResource($this->whenLoaded('author')),
                'itineraries' => ItineraryResource::collection($this->whenLoaded('ideaItineraries')),
                'place' => new OsmResource($this->whenLoaded('ideaPlace')),
                'places_to_visit' => OsmResource::collection($this->whenLoaded('ideaPlacesToVisit')),
                'price' => $this->whenLoaded('price', function(){
                    return new IdeaPriceResource($this->minimalFuturePrice);
                }),
                'dates' => $this->id ? DateResource::collection($this->whenLoaded('ideaDates')) : [],
                'future_dates' => $this->id ? DateResource::collection($this->whenLoaded('futureDates')) : [],
                'tags' => $this->id ? TaggedResource::collection($this->whenLoaded('tagged')) : [],
                'booking_params' => new BookingParam($this->whenLoaded('bookingParams')),
            ],
        ];
    }
}
