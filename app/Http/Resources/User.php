<?php

namespace App\Http\Resources;

use Auth;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'type' => 'users',
            'hid' => $this->hid,
            'locale' => LocaleMiddleware::getLocale(),

            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'profile_hid' => $this->profile->hid,
                'display_name' => $this->displayName,
                'image_url' => $this->imageUrl,
                'image_url_2' => mb_strtolower(class_basename(get_class($this->profile))) . '/' . $this->profile->id . '/' . htmlspecialchars(strip_tags($this->thmb_file_name)),
                'superuser' => Auth::user() ? $this->when(Auth::user()->hasAnyRole(['admin', 'super-admin']), true):null,
            ],

            'relationships' => [

            ],
        ];
    }
}
