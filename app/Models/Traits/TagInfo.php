<?php

namespace App\Models\Traits;

use Conner\Tagging\Model\Tag;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Tag as TagResource;


trait TagInfo
{


    public static function allMainTagsCollectionCached()
    {
        $mainTags = Cache::remember('mainTags', 10, function () {
            return Tag::whereIn('tag_group_id', [1,2,3])->get();
        });

        return response(TagResource::collection($mainTags));
    }

    public static function allMainTagsCollection()
    {
        $mainTags = Tag::whereIn('tag_group_id', [1,2,3])->get();

        return response(TagResource::collection($mainTags));
    }

    public function getItemTags()
    {
        return $this->tags;
    }

    /**
     * Old version
     * @return array
     */
    public function getItemTags2()
    {
        // негруппированные тэги ч-з запятую
        $simpleTagsText = '';

        // item grouped tags
        $itemSeasonsGroupTags = [];
        $itemAgeGroupTags = [];
        $itemDayTimeGroupTags = [];

        $tags = $this->tags;

        // загружаем тэги элемента и вносим их в группы
        foreach ($tags as $tag) {

            if ($tag->tag_group_id == '') {

                // строка тэгов, ч-з запятую
                $simpleTagsText == '' ? $simpleTagsText = $tag->name : $simpleTagsText = $simpleTagsText.','.$tag->name;

            } elseif ($tag->tag_group_id == 1) {
                $itemSeasonsGroupTags[] = $tag;
            } elseif ($tag->tag_group_id == 2) {
                $itemAgeGroupTags[] = $tag;
            } elseif ($tag->tag_group_id == 3) {
                $itemDayTimeGroupTags[] = $tag;
            }
        }

        return [
            'tagsText' => $simpleTagsText,
            'tagsSeasonsGroup' => TagResource::collection(collect($itemSeasonsGroupTags)),
            'tagsAgeGroup' => TagResource::collection(collect($itemAgeGroupTags)),
            'tagsDayTimeGroup' => TagResource::collection(collect($itemDayTimeGroupTags)),
        ];

    }

}
