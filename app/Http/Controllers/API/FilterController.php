<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\SeasonFilter;
use App\Classes\TimeFilter;

use App\Http\Requests\Filter\GetFilterItems;


class FilterController extends Controller
{
   public function activeItems(GetFilterItems $request, $filter)
   {
       if($filter === 'season'){
           $seasonFilter = new SeasonFilter();
       }

       if($filter === 'time'){
           $seasonFilter = new TimeFilter();

       }

       return response($seasonFilter->resourceCollection());
   }

}
