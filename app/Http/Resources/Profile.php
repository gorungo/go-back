<?php

namespace App\Http\Resources;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User  as UserResource;

class Profile extends JsonResource
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
            'type' => 'profiles',
            'hid' => $this->hid,

            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'url' => $this->url,
                'edit_url' => $this->editUrl,
                'image_url' => $this->imageUrl,
                'name' => $this->name ?? '',
                'description' => $this->description ?? '',
                'phone' => $this->phone ?? '',
                'site' => $this->site ?? '',
                'sex' => $this->sex ?? '',

                'booking_info' => $this->bookingInfo ? $this->bookingInfo->info : '',
                'booking_contacts' => $this->bookingInfo ? $this->bookingInfo->contacts : '',
            ],

            'relationships' => [
                'user' => new UserResource($this->whenLoaded('user')),
            ],
        ];
    }
}
