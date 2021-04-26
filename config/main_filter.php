<?php

return [

    'active_filters' => ['pl', 'season', 'time', 'distance'],

    // filter distance in km
    'distance' => [
        'default' => env('DISTANCE_DEFAULT', 200), // if no distance filter we will use this value

        'at' => 10,
        'close' => 35,
        'far' => 40000,
    ],
];

