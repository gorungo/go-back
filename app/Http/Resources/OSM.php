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
use App\Http\Resources\Tagged as TaggedResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OSM extends JsonResource
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
            'type' => $this->type,
            'id' => $this->id,
            'display_name' => $this->display_name,
            'title' => $this->title,
            'place_id' => $this->place_id,
            'icon' => $this->icon,
            'importance' => $this->importance,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'licence' => $this->licence,
            'osm_id' => $this->osm_id,
            'osm_type' => $this->osm_type,
        ];
    }
}
