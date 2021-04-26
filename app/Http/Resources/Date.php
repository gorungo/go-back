<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IdeaPrice as IdeaPriceResource;

class Date extends JsonResource
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
            'type' => 'dates',
            'id' => $this->id ,
            'locale' => LocaleMiddleware::getLocale() ,

            'attributes' => [
                'start_date' => $this->start_date,
                'start_time' => $this->start_time,
                'time_zone_offset' => $this->time_zone_offset,
            ],

            'relationships' => [
                'ideaPrice' => new IdeaPriceResource($this->whenLoaded('ideaPrice')),
            ]
        ];
    }
}
