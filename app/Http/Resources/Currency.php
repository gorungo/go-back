<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Address;
use App\Http\Resources\Address as AddressResource;
use App\Http\Resources\PlaceType as PlaceTypeResource;

class Currency extends JsonResource
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
            'type' => 'currencies',
            'id' => $this->id ,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'title' => $this->title,
                'code' => $this->code,
                'number' => $this->number,
            ],
        ];
    }
}
