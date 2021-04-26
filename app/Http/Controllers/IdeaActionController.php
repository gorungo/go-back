<?php

namespace App\Http\Controllers;

use App\Idea;
use App\Action;
use App\Category;
use App\Http\Requests\UploadPhoto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAction;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\DB;

class IdeaActionController extends Controller
{

    protected $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param String $categories
     * @param Idea $idea
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $categories, Idea $idea)
    {
        $categoriesUrl = $categories;
        $actions = $idea->actionItemsList();
        return view('idea.actions_index', compact(['idea', 'actions', 'categories']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // getting categories
        $categories = Category::get();


        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Новый товар',  'url' => '#'],
        ];*/


        return view('idea.create' , compact(['categories', 'breadcrumb_array']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreIdea $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIdea $request, Idea $idea)
    {

        $idea = $idea->createAndSync($request);

        if($idea){

            return redirect()->route('ideas.edit', $idea->slug )->with('status', __('idea.created'));

        }else{

            return redirect()->back()->withInput()->with('status', __('idea.not_created'));

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @param  Idea $item
     * @param  Action $action
     * @return \Illuminate\Http\Response
     */
    public function show( $categories, Idea $idea, Action $action)
    {
        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Новый товар',  'url' => '#'],
        ];*/

        return view('action.show' , compact(['idea', 'action', 'categories' , 'breadcrumb_array' ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Idea $idea
     * @return \Illuminate\Http\Response
     */
    public function edit(Idea $idea)
    {
        $item = $idea;
        $tagInfo = $item->getTagInfo();


        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Редактирование товара',  'url' => '#'],
        ];*/


        return view('idea.edit',  compact(['item' ,'tagInfo' , 'breadcrumb_array']));
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
