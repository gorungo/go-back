<?php

namespace App\Models;

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Requests\Post\StorePost;
use App\Models\Traits\Imageble;
use App\Models\Traits\TagInfo;
use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    use SoftDeletes;
    use Taggable;
    use TagInfo;
    use Imageble;

    protected $table = 'posts';

    protected $perPage = 60;

    protected $dates = ['deleted_at'];

    protected $fillable = ['author_id', 'parent_id', 'main_post_category_id', 'active', 'order', 'slug'];

    protected $with = ['localisedPostDescription', 'postMainCategory'];

    Public function getTitleAttribute()
    {
        if ($this->localisedPostDescription != null) {
            return $this->localisedPostDescription->title;
        } else {
            return '';
        }

    }

    Public function getIntroAttribute()
    {
        if ($this->localisedPostDescription != null) {
            return $this->localisedPostDescription->intro;
        } else {
            return '';
        }

    }

    Public function getDescriptionAttribute()
    {
        if ($this->localisedPostDescription != null) {
            return $this->localisedPostDescription->description;
        } else {
            return '';
        }

    }

    Public function getUrlAttribute()
    {

        if ($this->postMainCategory) {
            return route('posts.show', [$this->postMainCategory->pathToCategory(), $this->slug]);
        } else {
            return '';
        }

    }

    /**
     * Get path to tmb img of category item
     * @return string
     */
    Public function getImageUrlAttribute()
    {

        $defaultTmb = 'images/interface/placeholders/idea.png';

        if ($this->thmb_file_name != null) {
            //если есть картинка вакансии
            $src = 'storage/images/posts/' . $this->id . '/' . htmlspecialchars(strip_tags($this->thmb_file_name));

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

    public function getIsPublishedAttribute(){
        return $this->active == 1;
    }


    public function postDescriptions()
    {
        return $this->hasMany('App\Models\PostDescription', 'post_id', 'id');
    }


    public function localisedIdeaDescription()
    {
        return $this
            ->hasOne('App\Models\PostDescription', 'post_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }

    public function hasLocaleName($localeName)
    {
        return $this
            ->hasOne('App\Models\PostDescription', 'post_id', 'id')
            ->where('locale_id', LocaleMiddleware::getLocaleId($localeName))
            ->count();
    }

    public function hasLocaleId($localeId)
    {
        return $this
            ->hasOne('App\Models\PostDescription', 'post_id', 'id')
            ->where('locale_id', $localeId)->count();

    }

    /**
     * Основная категория для определения полного url идеи
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    /**
     * Main item category
     * @return mixed
     */
    public function postMainCategory()
    {
        return $this->belongsTo('App\Models\PostCategory', 'main_post_category_id');
    }

    public function getPostMainCategory()
    {
        return $this->postMainCategory()->first();
    }

    public function postCategories()
    {
        return $this
            ->belongsToMany('App\Models\PostCategory', 'post_post_category')
            ->using('App\Pivots\PostCategory');
    }

    public static function itemsList(Request $request, $activeCategory = Null)
    {
        $activeCategoryId = 0;

        if($activeCategory){
            $activeCategoryId = $activeCategory->id;
        }

        // получаем список активных идей с учетом города, страницы, локали
        return Cache::remember('posts_' . LocaleMiddleware::getLocale() . '_category_' . $activeCategoryId . '_' . request()->getQueryString(), 0, function () use ($activeCategory) {
            return self::whereCategory($activeCategory)
                ->joinDescription()
                ->WhereTags(MainFilter::getFiltersTagsArray())
                ->Sorting()
                ->paginate();
        });
    }


    private function generateSlug(String $title)
    {
        return str_slug($title);
    }

    public function createAndSync(StorePost $request)
    {

        $createResult = DB::transaction(function () use ($request) {

            $categoriesId = []; // ids of categories of idea item

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'author_id' => 1,
                'main_post_category_id' => $request->input('attributes.main_post_category_id'),
                'active' => $request->input('attributes.active'),
                'slug' => $this->generateSlug($request->input('attributes.title')),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $post = self::create($storeData);
            $post->localisedPostDescription()->create($descriptionStoreData);

            $post->updateRelationships($request);

            return $post;

        });

        return $createResult;
    }

    public function updateAndSync(StorePost $request)
    {

        $updateResult = DB::transaction(function () use ($request) {

            $localeId = LocaleMiddleware::getLocaleId();

            $storeData = [
                'author_id' => 1,
                'post_id' => $request->input('relationships.post.id'),
                'active' => $request->input('attributes.active'),
            ];

            $descriptionStoreData = [
                'title' => $request->input('attributes.title'),
                'intro' => $request->input('attributes.intro'),
                'description' => $request->input('attributes.description'),
                'locale_id' => $localeId,
            ];

            $this->update($storeData);
            $this->localisedIdeaDescription()->update($descriptionStoreData);
            $this->updateRelationships($request);


            return $this;

        });

        return $updateResult;

    }

    private function updateRelationships(StorePost $request): void
    {
        $this->saveCategories($request);
        $this->saveTags($request);
    }

    private function saveCategories(StorePost $request): void
    {
        $categories = $request->input('relationships.postCategories');

        $categoriesIds = [];

        if ($categories) {
            foreach ($categories as $category) {
                $categoriesIds[] = $category['id'];
            }

        }

        if (count($categoriesIds) > 0) {
            $this->postCategories()->sync($categoriesIds);
        }

    }

    private function saveTags(StorePost $request): void
    {
        $tagsText = $request->input('relationships.tags.tagsText');

        // Составляем массив из тэгов, потом сохряняем
        if ($tagsText != '') {
            $tags = explode(",", $tagsText);
            foreach ($tags as $tag) {
                if ($tag != '') {
                    $validTags[] = trim($tag);
                }
            }
        }

        if (count($validTags)) $this->retag($validTags);


    }


    // scopes

    public function scopeIsActive($query)
    {

        return $query->where('posts.active', '1');
    }

    public function scopeWhereCategory($query, Category $activeCategory = null)
    {

        if ($activeCategory) {

            $childCategories = $activeCategory->allCategoryChildrenArray();

            return $query->whereIn('post.id', function ($query) use ($childCategories) {
                $query->select('post_id')
                    ->from('post_post_category')
                    ->whereIn('post_category_id', $childCategories);
            });
        } else {
            return $query;
        }

    }


    public function scopeJoinDescription($query)
    {
        return $query->join('post_descriptions', function ($join) {
            $join->on('post.id', '=', 'post_descriptions.post_id')
                ->where('locale_id', LocaleMiddleware::getLocaleId());
        })->select('posts.*', 'post_descriptions.title', 'post_descriptions.intro' );
    }

    public function scopeSorting($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeWhereTags($query, Array $tags)
    {
        return $query->withAllTags($tags);
    }

    public static function emptyTagsArray()
    {
        return [
            'tagsAgeGroup' => [],
            'tagsSeasonsGroup' => [],
            'tagsDayTimeGroup' => [],
        ];
    }
}
