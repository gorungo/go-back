<?php

namespace App\Models;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Middleware\LocaleMiddleware;
use \Conner\Tagging\Taggable;
use App\Models\Traits\Imageble;
use App\Models\Traits\TagInfo;
use App\Http\Requests\Action\StoreAction;
use App\Http\Requests\Photo\UploadPhoto;

class Action extends Model
{

    use SoftDeletes;
    use Taggable;
    use TagInfo;
    use Imageble;

    protected $table = 'actions';

    protected $perPage = 60;

    protected $dates = ['deleted_at'];

    protected $fillable = ['author_id', 'idea_id', 'active', 'slug'];

    protected $with = ['localisedActionDescription', 'actionPlaces', 'actionPrice'];

    protected $attributes = [
        'active' => 0,
        'slug' => '',
    ];


    public function author()
    {
        return $this->belongsTo('App\Models\User', 'author_id');
    }

    /**
     * Action idea
     * @return mixed
     */
    public function actionIdea()
    {
        return $this->belongsTo('App\Models\Idea', 'idea_id');
    }

    public function actionCategories()
    {
        return $this->actionIdea->ideaCategories()->get();
    }

    public function actionDates()
    {
        return $this->hasMany('App\Models\ActionDate');
    }

    public function actionPlace()
    {
        return $this->actionPlaces()->first();
    }

    public function actionPlaces()
    {
        return $this->belongsToMany('App\Models\Place', 'action_place', 'action_id', 'place_id');
    }

    public function actionPrice()
    {
        return $this->hasOne('App\Models\ActionPrice')
            ->withDefault([
                'price' => 0,
                'currency_id' => 3
            ]);
    }

    public function actionDescriptions()
    {
        return $this->hasMany('App\Models\ActionDescription', 'action_id', 'id');
    }


    public function localisedActionDescription()
    {
        return $this
            ->hasOne('App\Models\ActionDescription', 'action_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }


    Public function getTitleAttribute()
    {

        if ($this->localisedActionDescription != null) {
            return $this->localisedActionDescription->title;
        } else {
            $actionDescription = $this->actionDescriptions()->first();
            if ($actionDescription) {
                return $actionDescription->title;
            }
        }


        return '';
    }

    Public function getIntroAttribute()
    {

        if ($this->localisedActionDescription != null) {
            return $this->localisedActionDescription->intro;
        } else {
            $actionDescription = $this->actionDescriptions()->first();
            if ($actionDescription) {
                return $actionDescription->intro;
            }
        }


        return '';
    }

    Public function getDescriptionAttribute()
    {

        if ($this->localisedActionDescription != null) {
            return $this->localisedActionDescription->description;
        } else {
            $actionDescription = $this->actionDescriptions()->first();
            if ($actionDescription) {
                return $actionDescription->description;
            }
        }


        return '';
    }

    Public function getUrlAttribute()
    {
        if ($this->actionIdea && $this->actionIdea->ideaMainCategory) {
            return route('actions.show',
                [$this->actionIdea->ideaMainCategory->pathToCategory(), $this->actionIdea->slug, $this->slug]);
        } else {
            return '';
        }

    }

    public function getFullUrlAttribute()
    {
        return $this->url.MainFilter::queryString();
    }

    public function getEditUrlAttribute()
    {
        return route('actions.edit', [$this->slug]);
    }

    /**
     * Get path to tmb img of category item
     * @return string
     */
    Public function getTmbImgPathAttribute()
    {

        $defaultTmb = 'images/interface/placeholders/idea.png';

        if ($this->thmb_file_name != null) {
            //если есть картинка
            $src = 'storage/images/action/'.$this->id.'/'.htmlspecialchars(strip_tags($this->thmb_file_name));

        } else {
            if ($this->actionPlaces()->first()->thmb_file_name) {
                //если есть картинка вакансии
                $src = $this->actionPlaces()->first()->TmbImgPath;
            } else {
                if ($this->actionIdea()->thmb_file_name) {
                    $src = $this->actionIdea()->TmbImgPath;
                }
            }
        }

        if (!file_exists($src)) {
            $src = $defaultTmb;
        }

        return $src;
    }

    public function getIsPublishedAttribute()
    {
        return $this->active == 1;
    }


    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        if (request()->is('api/*')) {
            return 'id';
        } else {
            return 'slug';
        }
    }


    public function hasLocaleName($localeName)
    {
        return $this
            ->hasOne('App\Models\ActionDescription', 'action_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId($localeName))
            ->count();
    }

    public function hasLocaleId($localeId)
    {
        return $this
            ->hasOne('App\ActionDescription', 'action_id', 'id')
            ->where('locale_id', $localeId)->count();

    }


    public static function itemsList(Request $request, $activeCategory)
    {

        // получаем список активных идей с учетом города, страницы, локали
        return Cache::remember('actions_'.LocaleMiddleware::getLocale().'_'.$activeCategory.'_'.request()->getQueryString(),
            1, function () use ($activeCategory) {
                return Action::whereHas('actionIdea', function ($query) use ($activeCategory) {
                    $query
                        ->whereCategory($activeCategory)
                        ->WhereTags(MainFilter::getFiltersTagsArray()
                        );
                })
                    ->JoinDescription()
                    ->DateFilter()
                    ->Sorting()
                    ->paginate();
            });
    }

    private function generateSlug(String $title)
    {
        return str_slug($title);
    }

    public static function backgroundImage($activeCategory = null)
    {
        return '/images/bg/mountains_blue.svg';
    }

    public function createAndSync(StoreAction $request)
    {

        $createResult = DB::transaction(function () use ($request) {

            $categoriesId = []; // ids of categories of idea item

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'author_id' => 1,
                'idea_id' => $request->input('relationships.idea.id'),
                'active' => $request->input('attributes.active'),
                'slug' => $this->generateSlug($request->input('attributes.title')),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $action = self::create($storeData);
            $action->localisedActionDescription()->create($descriptionStoreData);

            $action->updateRelationships($request);

            return $action;

        });

        return $createResult;
    }

    public function updateAndSync(StoreAction $request)
    {
        $updateResult = DB::transaction(function () use ($request) {

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'author_id' => 1,
                'idea_id' => $request->input('relationships.idea.id'),
                'active' => $request->input('attributes.active'),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $this->update($storeData);
            if ($this->localisedActionDescription) {
                $this->localisedActionDescription()->update($descriptionStoreData);
            } else {
                $this->localisedActionDescription()->create($descriptionStoreData);
            }

            $this->updateRelationships($request);
            return $this;

        });

        return $updateResult;

    }

    private function updateRelationships(StoreAction $request): void
    {
        $this->savePlaces($request);
        $this->saveDates($request);
        $this->savePrice($request);
    }

    private function savePlaces(StoreAction $request): void
    {
        $places = $request->input('relationships.places');
        $placeIds = [];

        if (count($places)) {
            foreach ($places as $place) {
                if ($place['id'] != '') {
                    $placeIds[] = $place['id'];
                }
            }

            if (count($placeIds)) {
                $this->actionPlaces()->syncWithoutDetaching($placeIds);
            }

        }
    }

    private function savePrice(StoreAction $request): void
    {
        $actionPrice = $request->input('relationships.price');
        if ($actionPrice['id'] !== null) {
            $this->actionPrice()->whereId($actionPrice['id'])->update([
                'price' => (int) $actionPrice['attributes']['price'],
                'currency_id' => $actionPrice['relationships']['currency']['id'],
            ]);
        } else {
            $this->actionPrice()->create([
                'price' => (int) $actionPrice['attributes']['price'],
                'currency_id' => $actionPrice['relationships']['currency']['id'],
            ]);
        }
    }

    private function saveDates(StoreAction $request): void
    {
        $dates = $request->input('relationships.dates');

        if ($dates) {
            foreach ($dates as $date) {
                if ($date['id'] !== null) {
                    $this->actionDates()->whereId($date['id'])->update([
                        'start_datetime_utc' => $date['attributes']['start_datetime_utc'],
                        'end_datetime_utc' => $date['attributes']['end_datetime_utc'],
                        'time_zone_offset' => $date['attributes']['time_zone_offset'],
                        'is_all_day' => $date['attributes']['is_all_day'],
                        'duration' => $date['attributes']['duration'],
                        'is_recurring' => $date['attributes']['is_recurring'],
                        'recurrence_pattern' => $date['attributes']['recurrence_pattern'],
                    ]);
                } else {
                    $this->actionDates()->create([
                        'start_datetime_utc' => $date['attributes']['start_datetime_utc'],
                        'end_datetime_utc' => $date['attributes']['end_datetime_utc'],
                        'time_zone_offset' => $date['attributes']['time_zone_offset'],
                        'is_all_day' => $date['attributes']['is_all_day'],
                        'duration' => $date['attributes']['duration'],
                        'is_recurring' => $date['attributes']['is_recurring'],
                        'recurrence_pattern' => $date['attributes']['recurrence_pattern'],
                    ]);
                }
            }

        }
    }

    // scopes

    public function scopeJoinDescription($query)
    {
        return $query->join('action_descriptions', function ($join) {
            $join->on('actions.id', '=', 'action_descriptions.action_id')
                ->where('locale_id', LocaleMiddleware::getLocaleId());
        })->select('actions.*', 'action_descriptions.title', 'action_descriptions.intro');
    }

    public function scopeIsActive($query)
    {

        return $query->where('active', '1');
    }


    public function scopeSorting($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeWhereTags($query, Array $tags)
    {
        return $query->withAllTags($tags);
    }

    public function scopeDateFilter($query)
    {
        return $query->inFuture()->inProgress();
    }

    public function scopeInFuture($query)
    {
        return $query->whereHas('actionDates', function ($query) {
            $query->whereRaw("TO_DAYS(NOW()) < TO_DAYS(`start_datetime_utc`) AND (TO_DAYS(NOW()) < TO_DAYS(`end_datetime_utc`) AND (`end_datetime_utc` is not null))")
                ->orWhereRaw("TO_DAYS(NOW()) < TO_DAYS(`start_datetime_utc`) AND `end_datetime_utc` is null");
        })->orDoesntHave('actionDates');
    }

    public function scopeInProgress($query)
    {
        return $query->orWhereHas('actionDates', function ($query) {
            $query->whereRaw("TO_DAYS(NOW()) > TO_DAYS(`start_datetime_utc`) AND TO_DAYS(NOW()) < TO_DAYS(`end_datetime_utc`) AND TO_DAYS(`start_datetime_utc`) != TO_DAYS(`end_datetime_utc`)");
        })->orDoesntHave('actionDates');
    }


}
