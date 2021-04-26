<?php

namespace App\Models;

use App\Http\Requests\Idea\StoreIdea;
use DB;
use App\Http\Requests\Category\StoreCategory;
use App\Models\Traits\Imageble;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Category extends Model
{
    use SoftDeletes;
    use Imageble;

    protected $table = 'categories';
    protected $dates = ['deleted_at'];
    protected $fillable = ['author_id', 'parent_id', 'active', 'order', 'slug'];
    protected $with = ['localisedCategoryTitle'];

    protected $needCache = false;
    protected $cacheTimeMinutes = 1;

    Public function getTitleAttribute()
    {
        if ($this->localisedCategoryTitle != null) {
            return $this->localisedCategoryTitle->title;
        } else {
            return '';
        }

    }

    Public function getIntroAttribute()
    {
        if ($this->localisedCategoryTitle != null) {
            return $this->localisedCategoryTitle->intro;
        } else {
            return '';
        }

    }

    Public function getDescriptionAttribute()
    {
        if ($this->localisedCategoryDescription != null) {
            return $this->localisedCategoryDescription->description;
        } else {
            return '';
        }

    }

    Public function getCategoryPathAttribute()
    {
        return '';
    }

    /**
     * Get path to tmb img of category item
     * @return string
     */
    Public function getTmbImgPathAttribute()
    {

        $defaultTmb = 'images/interface/icons/category_tmb_placeholder.png';

        if ($this->thmb_img != '') {
            //если есть картинка вакансии
            $src = 'storage/images/category/' . $this->id . '/' . htmlspecialchars(strip_tags($this->tmb_img));
        } else {
            //если есть картинка вакансии
            $src = $defaultTmb;
        }

        if (!file_exists($src)) {
            $src = $defaultTmb;
        }

        return $src;
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

    public function categoryAuthor()
    {
        return $this->belongsTo('App\Models\User', 'author_id');
    }


    public function categoryTitles()
    {
        return $this->hasMany('App\Models\CategoryTitle', 'category_id', 'id');
    }

    public function localisedCategoryTitle()
    {
        return $this->hasOne('App\Models\CategoryTitle', 'category_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }


    public function categoryDescriptions()
    {
        return $this->hasMany('App\Models\CategoryDescription', 'category_id', 'id');
    }

    public function localisedCategoryDescription()
    {
        return $this->hasOne('App\Models\CategoryDescription', 'category_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    public function hasLocaleName($localeName)
    {
        return $this->hasOne('App\Models\CategoryTitle', 'category_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId($localeName))->count();
    }

    public function hasLocaleId($localeId)
    {
        return $this->hasOne('App\Models\CategoryTitle', 'category_id', 'id')
            ->where('locale_id', $localeId)->count();

    }

    public function categoryMetas()
    {
        return $this->hasMany('App\Models\CategoryMeta', 'category_id', 'id');
    }

    public function localisedCategoryMetas()
    {
        return $this->hasMany('App\Models\CategoryMeta', 'category_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    public function categoryIdeas()
    {
        return $this->belongsToMany('App\Models\Idea')->using('App\Pivots\Category');
    }

    public function categoryActions()
    {
        return $this->hasManyThrough('App\Models\Action', 'App\Idea');
    }

    public function categoryParent()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id');

    }

    public function categoryChildren()
    {
        return $this->hasMany('App\Models\Category', 'parent_id');
    }

    public static function getMainCategories()
    {
        return Cache::tags(['category', 'mainIdeaCategories'])
            ->remember('mainIdeaCategories-' . LocaleMiddleware::getLocaleId(), 1, function () {

            return self::MainCategory()
                ->JoinDescription()
                ->IsActive()
                ->orderBy('order', 'desc')
                ->get();

        });

    }

    public static function getCategoriesForSelector($activeCategory){

        $categories = null;

        if ($activeCategory){
            $categories = Category::getChildCategoriesOf($activeCategory);
            if(!$categories || !$categories->count()){

                // if category doesn't have subcategories
                // loading subcategories of parent ( brothers and sisters ;)

                if($activeCategory->categoryParent){
                    $categories = Category::getChildCategoriesOf($activeCategory->categoryParent);
                }
            }
        }

        if(!$categories || !$categories->count()){
            $categories = Category::getMainCategories();
        }

        return $categories;
    }

    /**
     * Get subcategories of active category
     * @param Category $category
     * @return mixed
     */

    public static function getChildCategoriesOf(Category $category)
    {
        return Cache::remember('childIdeaCategoriesOf-' . $category->id . '-' . LocaleMiddleware::getLocaleId(), $category->cacheTimeMinutes, function () use($category) {

                return self::ChildCategory($category->id)
                    ->JoinDescription()
                    ->IsActive()
                    ->orderBy('order', 'desc')
                    ->get();

            });

    }

    private function generateSlug(String $title)
    {
        return str_slug($title);
    }


    /**
     * returns category path with subcategories
     * @return string
     */
    public function pathToCategory()
    {

        $category = $this;

        $path = Cache::tags(['category'])->remember('pathToCategory_' . $this->id, 10, function () use ($category) {

            $path = '';

            if ($category->id) {

                $path = $path . $category->slug;

                if ($category->parent_id !== 0) {
                    $path = $category->categoryParent->slug . '/' . $path;

                    if ($category->categoryParent->parent_id !== 0) {
                        $path = $category->categoryParent->categoryParent->slug . '/' . $path;
                    }
                }

            }

            return $path;
        });

        return $path;

    }

    public static function lastChildren()
    {
        return Category::whereDoesntHave('categoryChildren')->isActive()->get();
    }

    public static function categoryChildrenJSON($category = null)
    {

        if ($category == null) {
            $result = Category::MainCategory()->isActive()->get();

        } else {
            $result = Category::ChildCategory($category->id)->isActive()->get();

        }


        if (count($result)) {
            return response()->json($result);
        }

        return response()->json(['type' => 'error']);
    }


    public function scopeChildCategory($query, $parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }

    public function scopeParentCategories($query, $parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }


    public function CategoryFriends()
    {
        return Category::where('parent_id', $this->parent_id)->isActive()->get();
    }


    public function allCategoryChildrenArray()
    {

        $subcategoryCashLifeTime = 10;
        $needCache = true;

        $categoriesId = [];

        $categoriesId[] = $this->id;

        if (Cache::tags(['category'])->has('category_id_children_array_' . $this->id) && $needCache) {
            $categories_id = Cache::tags(['category'])->get('category_id_children_array_' . $this->id);
        } else {
            if (isset($this->categoryChildren)) {

                foreach ($this->categoryChildren as $children) {


                    if (isset($children->id)) $categoriesId[] = $children->id;

                    if (isset($children->categoryChildren)) {

                        foreach ($children->categoryChildren as $children_children) {


                            if (isset($children_children->id)) {

                                $categoriesId[] = $children_children->id;
                            }

                        }
                    }


                }
            } else {
                $categoriesId[] = $this->id;
            }

            Cache::tags(['category'])->put('category_id_children_array_' . $this->id, $categoriesId, $subcategoryCashLifeTime);
        }


        return $categoriesId;


    }

    public function allCategoryParentArray()
    {

        $categories_id = [];
        $needCashe = true;

        if (Cache::tags(['category','category_id_parent_array'])->has('category_id_parent_array_' . $this->id) && $needCashe) {
            $categories_id = Cache::tags(['category', 'category_id_parent_array'])->get('category_id_parent_array_' . $this->id);
        } else {
            if ($this->parent) {

                $parent = $this->parent;
                $categories_id[] = $parent->id;

                if (isset($parent->parent)) {

                    $categories_id[] = $parent->parent->id;

                }

                if (isset($parent->parent->parent)) {

                    $categories_id[] = $parent->parent->parent->id;

                }


            } else {
                $categories_id[] = 0;
            }

            Cache::tags(['category','category_id_parent_array'])->put('category_id_parent_array_' . $this->id, $categories_id, 0);
        }

        return $categories_id;


    }

    public function createAndSync(StoreCategory $request)
    {

        $createResult = DB::transaction(function () use ($request) {

            $categoriesId = []; // ids of categories of idea item

            $localeId = LocaleMiddleware::getLocaleId();

            $parenCategoryId = $request->input('attributes.categoryParent.id');

            $storeData = [
                'author_id' => Auth()->user()->id,
                'parent_id' => $parenCategoryId !== null ? $parenCategoryId : 0,
                'active' => $request->input('attributes.active'),
                'order' => $request->input('attributes.order'),
                'slug' => $this->generateSlug($request->input('attributes.title')),
            ];

            $titleStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'locale_id' => $localeId,
            ];

            $descriptionStoreData = [
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $category = self::create($storeData);
            $category->localisedCategoryTitle()->create($titleStoreData);
            $category->localisedCategoryDescription()->create($descriptionStoreData);

            $category->updateRelationships($request);

            return $category;

        });

        $this->clearAllCache();

        return $createResult;
    }

    public function updateAndSync(StoreCategory $request)
    {

        $updateResult = DB::transaction(function () use ($request) {

            $localeId = LocaleMiddleware::getLocaleId();
            $parenCategoryId = $request->input('attributes.categoryParent.id');

            $storeData = [
                'author_id' => Auth()->user()->id,
                'parent_id' => $parenCategoryId !== null ? $parenCategoryId : 0,
                'active' => $request->input('attributes.active'),
                'order' => $request->input('attributes.order'),
            ];

            $titleStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'locale_id' => $localeId,
            ];

            $descriptionStoreData = [
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $this->update($storeData);

            if ($this->localisedCategoryTitle) {
                $this->localisedCategoryTitle()->update($titleStoreData);
            } else {
                $this->localisedCategoryTitle()->create($titleStoreData);
            }

            if ($this->localisedCategoryDescription) {
                $this->localisedCategoryDescription()->update($descriptionStoreData);
            } else {
                $this->localisedCategoryDescription()->create($descriptionStoreData);
            }

            //$this->updateRelationships($request);

            return $this;

        });

        $this->clearAllCache();
        return $updateResult;

    }

    private function updateRelationships(StoreCategory $request): void
    {
        //$this->saveCategories($request);
        //$this->saveTags($request);
    }

    // scopes

    public function scopeMainCategory($query)
    {
        return $query->where('parent_id', '0');
    }

    public function scopeWhereParentSlug($query, $parentSlug)
    {

        $parentId = Category::where('slug', $parentSlug)->where('active', '1')->first();
        return $query->where('parent_id', $parentId);
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', '1');
    }

    public function clearAllCache()
    {
        Cache::tags('category')->flush();
    }

    public function scopeJoinDescription($query)
    {
        return $query->join('category_titles', function ($join) {
            $join->on('categories.id', '=', 'category_titles.category_id')
                ->where('locale_id', LocaleMiddleware::getLocaleId());
        })->select('categories.*', 'category_titles.title');
    }


}
