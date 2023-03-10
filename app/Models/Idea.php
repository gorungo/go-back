<?php

namespace App\Models;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Requests\Idea\StoreIdea;
use App\Models\Traits\Hashable;
use App\Models\Traits\Imageble;
use App\Models\Traits\TagInfo;
use DB;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Idea extends Model
{
    use SoftDeletes, TagInfo, Imageble, Hashable, SpatialTrait;

    const hidLength = 20;
    public $defaultTmb = null;
    protected $table = 'ideas';
    protected $perPage = 60;
    protected $dates = ['deleted_at'];
    protected $fillable = ['author_id', 'idea_id', 'parent_id', 'main_category_id', 'active', 'order', 'slug', 'idea_type_id'];
    protected $with = ['localisedIdeaDescription', 'ideaMainCategory', 'ideaPlace'];
    protected $spatialFields = [
        'coordinates',
    ];

    /**
     * Create empty instance in database
     * @return Idea $newIdea
     */
    public static function createEmptyOfUser(User $user): Idea
    {
        $newIdea = self::create([
            'author_id' => $user->id,
            'slug' => 'new_idea_slug',
        ]);

        $newIdea->slug = 'new_idea_slug_'.$newIdea->id;
        $newIdea->localisedIdeaDescription()->create([
            'locale_id' => LocaleMiddleware::getLocaleId(),
        ]);
        $newIdea->save();

        return $newIdea;
    }

    public static function itemsOfUser(User $user)
    {
        return Cache::tags(['ideas'])->remember('ideas_of_user_'.$user->id.'_'.LocaleMiddleware::getLocale().'_category_',
            0, function () use ($user) {
                return $user
                    ->ideas()
                    ->joinDescription()
                    ->hasImage()
                    ->get();
            });
    }

    public static function backgroundImage()
    {
        return null;
        //return '/images/bg/mountains_blue.svg';
    }

    public static function randomIdea()
    {
        return self::inRandomOrder()->first();
    }

    public static function emptyTagsArray()
    {
        return [
            'tagsAgeGroup' => [],
            'tagsSeasonsGroup' => [],
            'tagsDayTimeGroup' => [],
        ];
    }

    public static function getMain()
    {
        return self::main()->take(50)->get();
    }

    public function getIsPublishedAttribute()
    {
        return $this->approved_at && $this->active === 1;
    }

    public function getTitleAttribute()
    {
        if ($this->localisedIdeaDescription != null) {
            return $this->localisedIdeaDescription->title;
        } else {
            $ideaDescription = $this->ideaDescriptions()->first();
            if ($ideaDescription) {
                return $ideaDescription->title;
            }
        }

    }

    public function ideaDescriptions()
    {
        return $this->hasMany('App\Models\IdeaDescription', 'idea_id', 'id');
    }

    public function getIntroAttribute()
    {
        if ($this->localisedIdeaDescription != null) {
            return $this->localisedIdeaDescription->intro;
        } else {
            $ideaDescription = $this->ideaDescriptions()->first();
            if ($ideaDescription) {
                return $ideaDescription->intro;
            }
        }

    }

    public function getDescriptionAttribute()
    {
        if ($this->localisedIdeaDescription != null) {
            return $this->localisedIdeaDescription->description;
        } else {
            $ideaDescription = $this->ideaDescriptions()->first();
            if ($ideaDescription) {
                return $ideaDescription->description;
            }
        }

    }

    public function getHasIdeasAttribute()
    {
        return $this->futureIdeaIdeas()->count();
    }

    public function getIsBlockedAttribute()
    {
        return $this->active == 0;
    }

    public function getCanBeOrderedAttribute(): bool
    {
        return $this->ideaPrice->default == false;
    }

    public function ideaPlace(): BelongsTo
    {
        return $this->belongsTo('App\Models\OSM', 'place_id', 'id');
    }

    public function bookingParams(): HasOne
    {
        return $this->hasOne('App\Models\BookingParam')->withDefault([
            'info' => '',
            'contacts' => '',
            'whatsapp' => '',
        ]);
    }

    public function ideaItinerary()
    {
        return null;
    }

    /**
     * Date to display on idea card
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getDateToDisplayAttribute()
    {
        $date = $this->ideaDates()->inFuture()->first();
        return $date ? $date->startDateTimeUtc : null;
    }

    public function ideaDates()
    {
        return $this->hasMany('App\Models\IdeaDate');
    }

    public function futureDates()
    {
        return $this->hasMany('App\Models\IdeaDate')->whereRaw("TO_DAYS(`start_date`) >= TO_DAYS(NOW())");
    }

    /**
     * ???????????????? ?????????????????? ?????? ?????????????????????? ?????????????? url ????????
     * @return BelongsTo
     */

    public function ideaParentIdea(): BelongsTo
    {
        return $this->belongsTo('App\Models\Idea', 'idea_id')->isActive();
    }

    public function price()
    {
        return $this->hasOne('App\Models\IdeaPrice');
    }

    public function minimalFuturePrice()
    {
        return $this->ideaPrice()->whereHas('ideaDate', function ($q) {
            return $q->InFuture();
        })->orderBy('price', 'asc');
    }

    public function ideaPrice()
    {
        return $this->hasOne('App\Models\IdeaPrice')
            ->withDefault([
                'price' => 0,
                'currency_id' => 3,
                'default' => true,
            ]);
    }

    public function hasLocaleName($localeName)
    {
        return $this
            ->hasOne('App\Models\IdeaDescription', 'idea_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId($localeName))
            ->count();
    }

    public function hasLocaleId($localeId)
    {
        return $this
            ->hasOne('App\Models\IdeaDescription', 'idea_id', 'id')
            ->where('locale_id', $localeId)->count();

    }

    public function getIdeaMainCategory()
    {
        return $this->ideaMainCategory()->first();
    }

    /**
     * Main item category
     * @return mixed
     */
    public function ideaMainCategory()
    {
        return $this->belongsTo('App\Models\Category', 'main_category_id');
    }

    public function ideaAuthor()
    {
        return $this->belongsTo('App\Models\User', 'author_id');
    }

    public function author()
    {
        return $this->belongsTo('App\Models\User', 'author_id');
    }

    public function ideaIdeasList()
    {
        return $this->ideaIdeas()->isActive()->get();
    }

    /**
     * Get idea actions
     * @param  int  $itemsCount
     * @return mixed
     */
    public function ideaIdeasListLimited($itemsCount = 4)
    {
        return $this->ideaIdeas()->inFuture()->isActive()->take($itemsCount)->get();
    }

    public function createAndSync(StoreIdea $request)
    {

        $createResult = DB::transaction(function () use ($request) {

            $categoriesId = []; // ids of categories of idea item

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'author_id' => Auth()->User()->id,
                'idea_id' => $request->input('relationships.idea.id'),
                'active' => $request->input('attributes.active'),
                'slug' => $this->generateSlug($request->input('attributes.title')),
            ];

            if ($request->input('attributes.main_category_id') !== null) {
                $storeData ['main_category_id'] = $request->input('attributes.main_category_id');
            }

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $idea = self::create($storeData);
            $idea->localisedIdeaDescription()->create($descriptionStoreData);

            $idea->updateRelationships($request);

            return $idea;

        });

        return $createResult;
    }

    private function generateSlug(string $title)
    {
        return Str::slug($title);
    }

    public function updateAndSync(StoreIdea $request)
    {

        $updateResult = DB::transaction(function () use ($request) {

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'idea_type_id' => $request->input('attributes.idea_type_id'),
                'active' => $request->input('attributes.active'),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $this->update($storeData);

            if ($this->localisedIdeaDescription) {
                $this->localisedIdeaDescription()->update($descriptionStoreData);
            } else {
                $this->localisedIdeaDescription()->create($descriptionStoreData);
            }

            $this->updateRelationships($request);

            return $this;

        });

        return $updateResult;

    }

    public function localisedIdeaDescription()
    {
        return $this
            ->hasOne('App\Models\IdeaDescription', 'idea_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    private function updateRelationships(Request $request): void
    {
        $r = $request->input('relationships');

        $this->saveCategories($r['categories']);
        $this->saveItineraries($r['itineraries']);
        $this->savePlace($r['place']);
        $this->savePlacesToVisit($r['places_to_visit']);
        $this->saveDates($r['dates']);

        $this->saveOptions($request->input('attributes.options'));
    }

    private function saveCategories($categories): void
    {
        $categoriesIds = [];
        $firstCategoryId = null;

        if ($categories && count($categories) > 0) {
            $firstCategoryId = $categories[0]['id'];
            foreach ($categories as $category) {
                $categoriesIds[] = $category['id'];
            }
        }

        if (count($categoriesIds) > 0) {
            $this->ideaCategories()->sync($categoriesIds);
            // set main category
            $category = Category::find($firstCategoryId);
            $parentCategory = Category::getMainCategoryOfCategory($category);
            if($parentCategory){
                $this->main_category_id = $parentCategory->id;
                $this->save();
            }

        }

    }

    public function ideaCategories(): BelongsToMany
    {
        return $this
            ->belongsToMany('App\Models\Category', 'idea_category')
            ->using('App\Models\Pivots\Category');
    }

    private function saveItineraries($itineraries): void
    {
        $usedItinerariesIds = [];
        if ($itineraries) {
            foreach ($itineraries as $itinerary) {
                if ($itinerary['attributes']) {
                    $descriptionStoreData = [
                        'title' => $itinerary['attributes']['title'],
                        'description' => $itinerary['attributes']['description'],
                        'what_included' => $itinerary['attributes']['what_included'],
                        'will_visit' => $itinerary['attributes']['will_visit'],
                        'locale_id' => LocaleMiddleware::getLocaleId(),
                    ];

                    // todo
                    // ???? ???????????? ????????????????????, ?????????? ?????????????????? ?????????????????? id
                    // ?????????????????????????? ?????? ?????????????? ?????? ?????? ????????????????

                    if ($itinerary['id'] !== null && $itinerary['id'] !== 0) {

                        // update existed
                        $itineraryObj = Itinerary::find($itinerary['id']);
                        $itineraryObj->start_time = $itinerary['attributes']['start_time'];
                        $itineraryObj->day_num = $itinerary['attributes']['day_num'];
                        $itineraryObj->day_order = $itinerary['attributes']['day_order'];

                        if ($itineraryObj->localisedItineraryDescription) {
                            $itineraryObj->localisedItineraryDescription()->update($descriptionStoreData);
                        } else {
                            $itineraryObj->localisedItineraryDescription()->create($descriptionStoreData);
                        }

                        //$itineraryObj->localisedItineraryDescription()->updateOrCreate($descriptionStoreData);
                        $itineraryObj->save();

                    } else {

                        // create new
                        $itineraryObj = $this->ideaItineraries()->create([
                            'idea_id' => request()->input('id'),
                            'start_time' => $itinerary['attributes']['start_time'],
                            'day_num' => $itinerary['attributes']['day_num'],
                        ]);

                        $itineraryObj->localisedItineraryDescription()->create($descriptionStoreData);
                    }
                    $usedItinerariesIds[] = $itineraryObj->id;
                }

            }

        }

        $this->ideaItineraries()->whereNotIn('id', $usedItinerariesIds)->delete();
    }

    public function ideaItineraries(): HasMany
    {
        return $this->hasMany('App\Models\Itinerary')
            ->orderBy('day_num', 'asc');
    }

    private function savePlace($place): void
    {
        $newPlace = null;
        if ($place) {
            if (isset($place['id'])) {
                $this->place_id = $place['id'];
            } else {
                $newPlace = OSM::createFrom($place);
                $this->place_id = $newPlace->id;
            }
        } else {
            $this->place_id = null;
        }
        $this->save();
    }

    private function saveBookingParams($bookingParams): void
    {
        $newPlace = null;
        if ($bookingParams) {
            $data = $bookingParams['attributes'];
            if (isset($bookingParams['id'])) {
                $this->bookingParams()->update($data);
            } else {
                $this->bookingParams()->create($data);
            }
        } else {
            $this->bookingParams()->delete();
        }
        $this->save();
    }

    private function savePlacesToVisit($places): void
    {
        $placeIds = [];

        if ($places && count($places)) {
            foreach ($places as $place) {
                if (isset($place['id'])) {
                    $placeIds[] = $place['id'];
                } else {
                    $newPlace = OSM::createFrom($place);
                    $placeIds[] = $newPlace->id;
                }
            }
        }
        $this->ideaPlacesToVisit()->sync($placeIds);
    }

    public function ideaPlacesToVisit(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\OSM', 'idea_place', 'idea_id', 'place_id');
    }

    private function saveDates($dates): void
    {
        $usedDateIds = [];

        if ($dates) {
            foreach ($dates as $date) {

                $ideaDate = null;
                $ideaPriceArray = $date['relationships']['ideaPrice'];

                if ($date['id'] !== null && strlen((string) $date['id']) < 13) {
                    $ideaDate = $this->ideaDates()->find($date['id']);
                    $ideaDate->update([
                        'start_date' => $date['attributes']['start_date'],
                        'start_time' => $date['attributes']['start_time'],
                        'time_zone_offset' => $date['attributes']['time_zone_offset'],
                    ]);
                } else {
                    $ideaDate = $this->ideaDates()->create([
                        'start_date' => $date['attributes']['start_date'],
                        'start_time' => $date['attributes']['start_time'],
                        'time_zone_offset' => $date['attributes']['time_zone_offset'],
                    ]);
                }

                $usedDateIds[] = $ideaDate->id;

                // save date price
                if ($ideaPriceArray['id'] !== null) {
                    $this->ideaPrice()->whereId($ideaPriceArray['id'])->update([
                        'idea_date_id' => $ideaDate->id,
                        'idea_price_type_id' => 1,
                        'age_group_id' => 1,
                        'price' => (int) $ideaPriceArray['attributes']['price'] * 100,
                        'currency_id' => $ideaPriceArray['relationships']['currency']['id'],
                    ]);
                } else {
                    $this->ideaPrice()->create([
                        'idea_date_id' => $ideaDate->id,
                        'idea_price_type_id' => 1,
                        'age_group_id' => 1,
                        'price' => (int) $ideaPriceArray['attributes']['price'] * 100,
                        'currency_id' => $ideaPriceArray['relationships']['currency']['id'],
                    ]);
                }
            }

        }

        // remove not used dates from database
        $this->ideaDates()->whereNotIn('id', $usedDateIds)->forceDelete();

    }

    /*    private function savePrice(StoreIdea $request): void
        {
            $actionPrice = $request->input('relationships.price');
            if ($actionPrice['id'] !== null) {
                $this->ideaPrice()->whereId($actionPrice['id'])->update([
                    'price' => (int) $actionPrice['attributes']['price'],
                    'currency_id' => $actionPrice['relationships']['currency']['id'],
                ]);
            } else {
                $this->ideaPrice()->create([
                    'price' => (int) $actionPrice['attributes']['price'],
                    'currency_id' => $actionPrice['relationships']['currency']['id'],
                ]);
            }
        }*/

    private function saveOptions($options): void
    {
        if ($options) {
            $this->options = json_encode($options);
            $this->save();
        }
    }

    public function publish()
    {
        $this->approve();
        $this->active = 1;
        $this->save();
    }

    public function approve()
    {
        if (!$this->approved_at && config('app.auto_idea_approve')) {
            $this->approved_at = now();
            $this->save();
        }
    }

    public function unPublish()
    {
        $this->active = 0;
        $this->save();
    }

    public function updateRelationship(Request $request, string $type): void
    {
        switch ($type) {
            case 'categories' :
                $this->saveCategories($request->input('data'));
                break;
            case 'itineraries' :
                $this->saveItineraries($request->input('data'));
                break;

            case 'place' :
                $this->savePlace($request->input('data'));
                break;

            case 'places_to_visit' :
                $this->savePlacesToVisit($request->input('data'));
                break;

            case 'dates' :
                $this->saveDates($request->input('data'));
                break;

            case 'booking_params' :
                $this->saveBookingParams($request->input('data'));
                break;
        }
    }


    public function scopeIsActive($query)
    {
        return $query->where('ideas.active', 1);
    }

    /**
     * Item is approved by moderator
     * @param $query
     * @return mixed
     */
    public function scopeIsApproved($query)
    {
        return $query->whereNotNull('ideas.approved_at');
    }

    public function scopeOrderByStartDate($query)
    {
        $query->orderBy('start_date', 'asc');
    }


    /**
     * Item is published by owner
     * @param $query
     * @return mixed
     */
    public function scopeIsPublished($query)
    {
        return $query->isActive()->isApproved();
    }

    public function scopeWhereCategory($query)
    {
        $category = null;
        if (request()->has('category_id') && (int) request()->category_id) {
            $category = Category::find(request()->category_id);
        }
        if ($category) {
            $childCategories = $category->allCategoryChildrenArray();
            return $query->whereIn('ideas.id', function ($query) use ($childCategories) {
                $query->select('idea_id')
                    ->from('idea_category')
                    ->whereIn('category_id', $childCategories);
            });
        } else {
            return $query;
        }

    }

    public function scopeJoinDescription($query)
    {
        return $query->join('idea_descriptions', function ($join) {
            $join->on('ideas.id', '=', 'idea_descriptions.idea_id')
                ->where('locale_id', LocaleMiddleware::getLocaleId());
        })->select('ideas.*', 'idea_descriptions.title', 'idea_descriptions.intro');
    }

    public function scopeJoinPlace($query)
    {
        return $query->join('osms', 'ideas.place_id', '=', 'osms.id');
    }

    public function scopeWhereMainCategory($query)
    {
        if(request()->has('category_id')){
            return $query->where('main_category_id', request()->input('category_id'));
        }
        return $query;
    }

    public function scopeSorting($query)
    {
        $searchPoint = MainFilter::searchPoint();
        if ($searchPoint) {
            return $query->distance('coordinates', $searchPoint, MainFilter::searchDistance())
                ->orderByDistance('coordinates', $searchPoint, 'asc');
        }
        return $query;
    }

    public function scopeJoinIdeaDates($query)
    {
        return $query->leftJoin('idea_dates', function ($join) {
        $join
            ->on('ideas.id', '=', 'idea_dates.idea_id')
            ->whereRaw("TO_DAYS(NOW()) <= TO_DAYS(`start_date`)")->take(1);
    });
    }

    public function scopeWhereTags($query, array $tags)
    {
        return $query->withAllTags($tags);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', 1);
    }

    public function scopeDateFilter($query)
    {
        return $query->inFuture();
    }

    /**
     * Scope items will be in future
     * @param $query
     * @return mixed
     */
    public function scopeInFuture($query)
    {
        return $query->whereRaw("TO_DAYS(NOW()) <= TO_DAYS(`start_date`)");
    }

    /**
     * Scope main filter
     * @param $query
     * @return mixed
     */
    public function scopeWhereFilters($query)
    {
        return $query
            ->whereCategory()
            ->WherePlace()
            ->WhereDates()
            ->WherePrices();
    }

    /**
     * Scope filter main filter dates
     * @param $query
     * @return mixed
     */
    public function scopeWhereDates($query)
    {
        if (request()->has('date_from')) {
            $dateFrom = request()->input('date_from');
            $dateTo = request()->input('date_to');
            return $query
                ->whereDate('start_date', '>=', date_format(date_create($dateFrom), 'Y-m-d'))
                ->whereDate('start_date', '<=', date_format(date_create($dateTo), 'Y-m-d'));

        }

        return $query;
    }

    /**
     * Scope filter main filter prices
     * @param $query
     * @return mixed
     */
    public function scopeWherePrices($query)
    {
        return $query;
    }

    public function scopeHasImage($query)
    {
        return $query->whereNotNull('thmb_file_name');
    }

    public function scopeNotModerated($query)
    {
        return $query->whereNull('is_approved')->whereHas('IdeaApproval', function ($q) {
            $q->whereNull('moderated_at')->where(function ($q2) {
                $q2->where('moderator_id', null)->elseWhere('moderator_id', User::activeUser()->id);
            });
        });
    }

    /**
     * Scope ideas belonged to region or city
     * @param $query
     * @return mixed
     */
    public function scopeWherePlace($query)
    {
        if (request()->has('place_id')) {
            return $query->where('ideas.place_id', (int) request()->input('place_id'));
        }
        return $query;
    }

    public function scopeWherePlaceId($query, $placeId)
    {
        if ($placeId !== null) {
            return $query->whereHas('ideaPlace', function ($q) use ($placeId) {
                $q->where('places.id', $placeId);
            });
        }
        return $query;
    }

    private function saveTags($tags): void
    {
        $validTags = [];

        // ???????????????????? ???????????? ???? ??????????, ?????????? ??????????????????
        if ($tags && count($tags)) {
            foreach ($tags as $tag) {
                if ($tag['attributes']['name'] !== '') {
                    $validTags[] = trim($tag['attributes']['name']);
                }
            }
        }

        if (count($validTags)) {
            $this->retag($validTags);
        }

    }

    private function saveDatePrice(): void
    {

    }


}
