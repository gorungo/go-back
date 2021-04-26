<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Tag extends JsonResource
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
            'type' => 'tagging_tags',
            'id' => $this->id ,

            'attributes' => [
                'slug' => $this->slug,
                'name' => $this->name,
                'tag_group_id' => $this->tag_group_id,
            ],

            'relationships' => [],
        ];
    }
}
