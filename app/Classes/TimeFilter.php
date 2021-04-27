<?php

namespace App\Classes;


class TimeFilter extends Filter
{
    public $name = 'time';
    public $items = [];

    public function __construct()
    {
        $this->items = [
            ['' => __('menu.daytime_')],
            ['morning' => __('menu.daytime_morning')],
            ['day' => __('menu.daytime_day')],
            ['evening' => __('menu.daytime_evening')],
            ['night' => __('menu.daytime_night')],
        ];
    }
}
