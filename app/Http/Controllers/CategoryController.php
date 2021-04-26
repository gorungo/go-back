<?php

namespace App\Http\Controllers;

use App\CategoryTitle;
use App\CategoryDescription;
use App\Http\Requests\StoreCategory;
use App\Page;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\LocaleMiddleware;

class CategoryController extends Controller
{

    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $page = new Page();
        $page->title = __('category.create');


        return view('category.edit' , compact(['categories', 'page']));
    }

    /**
     * Store new resourse Data
     * @param StoreCategory $request
     * @param Category $category
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategory $request, Category $category)
    {

        $newCategory = $category->createAndStore($request);

        if($newCategory){
            // очищаем старый кэш
            //Cache::forget('category-all');
            //Cache::forget('category-widget');

            return redirect()->route('category.edit', $newCategory->slug )->with('status', __('category.created'));

        }else{
            return redirect()->back()->withInput()->with('status', __('category.not_created'));

        }



    }

    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('category.create' , compact(['categories','breadcrumb_array' ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $page = new Page();
        $page->title = __('category.edit');
        return view('category.edit',  compact(['page','category']));
    }

    /**
     * Updating data
     * @param StoreCategory $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreCategory $request, Category $category)
    {
        $updateResult = $category->updateAndStore($request);

        if($updateResult){
            // очищаем старый кэш
            //Cache::forget('category-all');
            //Cache::forget('category-widget');

            return redirect()->back()->withInput()->with('status', __('category.updated'));

        }else{
            return redirect()->back()->with('status', __('category.not_updated'));

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



}
