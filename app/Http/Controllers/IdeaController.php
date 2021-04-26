<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Resources\PlaceNoRelationships;
use App\Idea;
use App\Category;
use App\Http\Requests\Photo\UploadPhoto;
use App\Page;
use App\User;
use App\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Idea\StoreIdea;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class IdeaController extends Controller
{

    protected $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param String $categoriesUrl
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $categoriesUrl = null)
    {
        $page = new Page();
        $page->title = config('app.name') . ' - ' . __('idea.description') . '.';

        $activePlaceResource = null;
        $categoriesArray = null;
        $activeCategory = null;
        $subCategory = null;
        $categories = null;
        $ideas = null;

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

        $activePlace = Place::activePlace();
        if($activePlace){
            $activePlaceResource = new PlaceNoRelationships($activePlace);
        }
        $sectionTitle = __('place.title');

        if($activePlace){
            $sectionTitle =__('place.places_close_to') .' '. $activePlace->title;
        }

        $backgroundImage = Idea::backgroundImage($activeCategory);
        $categories = Category::getCategoriesForSelector($activeCategory);
        $ideas = Idea::itemsList($request, $activeCategory);

        return view('idea.index', compact([
            'page', 'ideas', 'activeCategory', 'categories', 'categoriesUrl', 'subCategory', 'backgroundImage',
            'sectionTitle', 'activePlace', 'activePlaceResource'
        ]));
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param String $categoriesUrl
     * @return \Illuminate\View\View
     */
    public function main(Request $request, $categoriesUrl = null)
    {
        $page = new Page();
        $page->title = config('app.name') . ' - ' . __('idea.description') . '.';

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

        $activePlace = Place::activePlace();
        $activePlaceResource = $activePlace ? new PlaceNoRelationships($activePlace) : null;
        $sectionTitle = __('place.title');

        if($activePlace){
            $sectionTitle =__('place.places_close_to') .' '. $activePlace->title;
        }

        $backgroundImage = Idea::backgroundImage($activeCategory);
        $categories = Category::getCategoriesForSelector($activeCategory);
        $ideas = Idea::itemsList($request, $activeCategory);

        return view('idea.index', compact([
            'page', 'ideas', 'activeCategory', 'categories',
            'categoriesUrl', 'subCategory', 'backgroundImage',
            'sectionTitle', 'activePlace', 'activePlaceResource'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return RedirectResponse
     */
    public function new()
    {
        if(User::activeUser()->hasDraftIdeas()){
            return redirect()->route('office.ideas');
        }else{
            return redirect()->route('ideas.create');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return RedirectResponse
     */
    public function create()
    {
        $newIdea = Idea::createEmpty();
        return redirect()->route('ideas.edit', ['idea' => $newIdea]);
    }


    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @param  Idea $idea
     * @return \Illuminate\Http\Response
     */
    public function show( $category, Idea $idea)
    {
        $breadCrumbs = [];
        $ideaIdeas = $idea->ideaIdeasListLimited(4);

        if($idea->idea_id){
            $breadCrumbs[] = [
                'sectionTitle' => __('idea.title'),
                'itemUrl'=> $idea->ideaParentIdea->url, 'itemTitle'=> $idea->ideaParentIdea->title,
                'imgUrl' => $idea->ideaParentIdea->TmbImgPath
            ];
        }

        $breadCrumbs[] = [
            'sectionTitle' => __('idea.title'),
            'itemUrl'=> $idea->url,
            'itemTitle'=> $idea->title,
            'imgUrl' => $idea->idea_id ? null : $idea->TmbImgPath,
        ];


        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Новый товар',  'url' => '#'],
        ];*/

        return view('idea.show' , compact(['idea', 'category', 'ideaIdeas', 'breadCrumbs' ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Idea $idea
     * @return \Illuminate\Http\Response
     */
    public function edit(Idea $idea)
    {
        return view('idea.edit',  compact(['idea']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreIdea $request
     * @param  Idea $idea
     * @return \Illuminate\Http\Response
     */
    public function update(StoreIdea $request, Idea $idea)
    {

        $updateResult = $idea->updateAndSync($request);

        if($updateResult){
            // очищаем старый кэш
            //Cache::forget('category-all');
            //Cache::forget('category-widget');

            return redirect()->back()->with('status', __('idea.updated'));

        }else{
            return redirect()->back()->with('status', __('idea.not_updated'));

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
