<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Currency as CurrencyResource;


class ActionPrice extends JsonResource
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
            'type' => 'ActionPrice',
            'id' => $this->id ,
            'locale' => LocaleMiddleware::getLocale() ,

            'attributes' => [
                'price' => $this->formattedPrice,
                'currency_id' => $this->currency_id,
            ],

            'relationships' => [
                'currency' => new CurrencyResource($this->currency),
            ],

        ];
    }
}
