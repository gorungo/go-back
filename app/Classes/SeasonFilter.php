<?php

namespace App\Classes;


class SeasonFilter extends Filter
{
    public $name = 'season';
    public $items = [];

    public function __construct()
    {
        $this->items = [
            ['' => __('menu.season_')],
            ['spring' => __('menu.season_spring')],
            ['summer' => __('menu.season_summer')],
            ['autumn' => __('menu.season_autumn')],
            ['winter' => __('menu.season_winter')],
        ];
    }
}
