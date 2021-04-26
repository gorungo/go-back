<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceNoRelationships;
use App\Idea;
use App\Page;
use App\Action;
use App\Category;
use App\Http\Requests\Photo\UploadPhoto;
use App\Place;
use Illuminate\Http\Request;
use App\Http\Requests\Action\StoreAction;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{

    protected $page;
    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $categoriesUrl = null)
    {
        $page = new Page();
        $page->title = config('app.name') . ' - ' . __('action.description') . '.';

        $categoriesArray = null;
        $activeCategory = null;
        $subCategory = null;
        $categories = null;

        $categoriesArray = explode('/', $categoriesUrl);
        $activeCategorySlug = last($categoriesArray);

        if ($activeCategorySlug) {

            $activeCategory = Category::where('slug', mb_strtolower($activeCategorySlug))->first();
            if(!$activeCategory){
                abort('404');
            }
            $subCategory = $activeCategory->categoryParent;

            if($activeCategory)
                $page->title = $page->title . ' ' . $activeCategory->title . '.';
        }

        $categories = Category::getCategoriesForSelector($activeCategory);
        $ideas = Idea::itemsList($request, $activeCategory);
        $backgroundImage = Action::backgroundImage($activeCategory);
        $activePlace = Place::activePlace();
        $activePlaceResource = $activePlace ? new PlaceNoRelationships($activePlace) : null;

        // get list of actions
        $actions = Action::itemsList($request, $activeCategory);

        return view('action.index', compact([
            'page', 'actions', 'categories', 'activeCategory', 'subCategory', 'backgroundImage',
            'activePlace', 'activePlaceResource'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Idea $idea, Action $action)
    {
        return view('action.edit' , compact(['action', 'idea']));
    }

    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @param  Idea $item
     * @return \Illuminate\Http\Response
     */
    public function show( $category, $itemSlug)
    {

        $item = Idea::where('slug', $itemSlug)->first();

        if(!$item){
            abort('404');
        }

        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Новый товар',  'url' => '#'],
        ];*/

        return view('idea.show' , compact(['item', 'breadcrumb_array' ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Action $action
     * @return \Illuminate\Http\Response
     */
    public function edit(Action $action)
    {
        return view('action.edit',  compact(['action']));
    }



    /**
     * Return list of items photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPhotosListJson(){
        return response()->json($this->idea->ideaPhotos()->isActive()->get());
    }

    /**
     * Return list of items photo
     * @param UploadPhoto $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(UploadPhoto $request, $itemId){

        $idea = Idea::where('id', $itemId)->first();
        if($idea) return response()->json($idea->uploadPhoto($request));

        return response()->json(['type' => 'error', 'itemId' => $itemId]);
    }
}
