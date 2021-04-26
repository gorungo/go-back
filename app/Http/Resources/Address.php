<?php

namespace App\Http\Resources;

use App\Place;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;

class Address extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(!$this->id){
            $lastPlace = Place::lastCreatedPlaceOfAuthUser();
        }

        return [
            'type' => 'address',
            'id' => $this->id,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'postal_code' => $this->id ? $this->postal_code : $this->defaultAddressAttributes()['postal_code'],
                'country_code' => $this->id ? $this->country_code : $this->defaultAddressAttributes()['country_code'],

                'country' => $this->id ? $this->country : $this->defaultAddressAttributes()['country'],
                'region' => $this->id ? $this->region : $this->defaultAddressAttributes()['region'],
                'city' => $this->id ? $this->city : $this->defaultAddressAttributes()['city'],
                'address' => $this->id ? $this->address : $this->defaultAddressAttributes()['address'],
            ],

            'relationships' => [
            ],
        ];
    }
}
