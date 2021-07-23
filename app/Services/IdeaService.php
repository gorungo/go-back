<?php


namespace App\Services;

use App\Models\Category;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class IdeaService
{
    /**
     * Ideas main list
     *
     * @param  Request  $request
     * @param  null  $activeCategory
     * @return mixed
     */
    public static function itemsList(Request $request)
    {
        return Cache::tags(['ideas'])->remember('ideas_'.request()->getQueryString(),
            10, function () use ($request) {

                return Idea::select(['ideas.*', 'idea_dates.start_date', 'osms.coordinates'])
                    ->joinPlace()
                    ->joinIdeaDates()
                    ->inFuture()
                    ->whereFilters()
                    ->hasImage()
                    ->isPublished()
                    ->distinct()
                    ->orderByStartDate()
                    ->paginate($request->limit);
            });
    }

    /**
     * Get ideas to show on main page sections
     *
     * @param  Request  $request
     * @param  int  $itemsCount
     * @return mixed
     */
    public static function widgetMainItemsList(Request $request, int $itemsCount = 6)
    {
        return Cache::tags(['ideas'])->remember('ideas_widget_'.$itemsCount.'_'.request()->getQueryString(),
            60, function () use ($itemsCount, $request) {
                return Idea::joinPlace()
                    ->joinIdeaDates()
                    ->select(['ideas.*', 'idea_dates.start_date', 'osms.coordinates'])
                    ->inFuture()
                    ->whereFilters()
                    ->hasImage()
                    ->isPublished()
                    ->take($itemsCount)
                    ->groupBy('ideas.id')
                    ->distinct()
                    ->orderByStartDate()
                    ->get()
                    ->loadMissing($request->has('include') && $request->input('include') != '' ? explode(',',
                        $request->include) : []);
            });
    }


}
