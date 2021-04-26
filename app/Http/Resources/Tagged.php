<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Tagged extends JsonResource
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
            'id' => $this->tag->id ,

            'attributes' => [
                'slug' => $this->tag->slug,
                'name' => $this->tag->name,
                'tag_group_id' => $this->tag->tag_group_id,
            ],

            'relationships' => [],
        ];
    }
}
