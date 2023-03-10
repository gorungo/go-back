<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Resources\Address as AddressResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Address;
use App\Http\Resources\PlaceType as PlaceTypeResource;
use App\Models\PlaceType;

class Place extends JsonResource
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
            'type' => 'places',
            'id' => $this->id ,
            'hid' => $this->hid,
            'locale' => LocaleMiddleware::getLocale() ,

            'attributes' => [
                'title' => $this->title,
                'country_title' => $this->country_title,
                'url' => $this->url,
                'place_type_id' => $this->place_type_id,

                'edit_url' => $this->editUrl,
                'tmb' => asset($this->tmbImgPath),
                'description' => $this->description,
                'intro' => $this->intro,
                'coordinates' =>$this->id ? $this->coordinates : [
                    'coordinates' => [null,null]
                ],

                'rating' => $this->rating ?? 0,
            ],

            'relationships' => [
                'placeType' => new PlaceTypeResource($this->whenLoaded('placeType')) ?? null,
                'address' => $this->placeAddress ? new AddressResource($this->placeAddress) : new AddressResource(new Address),
            ],

            'meta' => [
                'allPlaceTypes' => PlaceTypeResource::collection(PlaceType::all()),
            ],
        ];
    }
}
