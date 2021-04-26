<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Resources\Address as AddressResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Address;
use App\Http\Resources\PlaceType as PlaceTypeResource;
use App\PlaceType;

class Itinerary extends JsonResource
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
            'type' => 'itineraries',
            'id' => $this->id ,
            'locale' => $this->locale ? $this->locale->slug : null,

            'attributes' => [
                'title' => $this->title,
                'day_num' => $this->day_num,
                'day_order' => $this->day_order,
                'start_time' => $this->start_time,
                'description' => $this->description,
                'what_included' => $this->whatIncluded,
                'will_visit' => $this->willVisit,
                'image_url' => $this->fullTmbImgPath,
                'locale_id' => $this->locale ? $this->locale->id : null,
            ],

            'relationships' => [
            ],

        ];
    }
}
