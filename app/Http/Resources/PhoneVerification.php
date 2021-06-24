<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Address;
use App\Http\Resources\Address as AddressResource;
use App\Http\Resources\PlaceType as PlaceTypeResource;

class PhoneVerification extends JsonResource
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
            'type' => 'PhoneVerification',
            'id' => $this->id ,

            'attributes' => [
                'attempts' => $this->title,
                'code' => $this->code,
                'exp_date' => $this->exp_date,
            ],
        ];
    }
}
