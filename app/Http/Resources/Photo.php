<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Currency as CurrencyResource;


class Photo extends JsonResource
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
            'type' => 'Photo',
            'id' => $this->id ,
            'locale' => LocaleMiddleware::getLocale() ,

            'attributes' => [
                'order' => $this->order,
                'image_url' => $this->imageUrl,
                'image1x_url' => $this->image1xUrl,
                'image2x_url' => $this->image2xUrl,
                'image_name' => $this->image_name,
                'active' => $this->active,
            ],

            'relationships' => [

            ],

        ];
    }
}
