<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Requests\Category\StoreCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return CategoryResource::collection(Category::lastChildren());
    }

    public function lastChildren()
    {
        return CategoryResource::collection(Category::lastChildren());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return CategoryResource
     */
    public function create(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCategory $request
     * @return CategoryResource
     */
    public function store(StoreCategory $request, Category $category)
    {
        return new CategoryResource($category->createAndSync($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Category::findorfail($id)->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category $category
     * @return CategoryResource
     */
    public function edit(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreCategory $request
     * @return CategoryResource
     */
    public function update(StoreCategory $request, Category $category)
    {
        return new CategoryResource($category->updateAndSync($request));
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

    public function fullCategoriesListing($categoryId){

        $category = Category::where('id', $categoryId)->isActive()->first();

        if($category){
            return $category->fullCategoriesListing($category);
        }else if($categoryId == 0){
            $parenCategoryList[] = Category::where('parent_id',$categoryId)->isActive()->get();
            return response()->json(['type'=>'ok', 'data'=>['parentCategoryList' => $parenCategoryList, 'categorySelected' => [0]]]);
        }else{
            return ['type' => 'error'];
        }


    }
    public function child($categoryId){

        $category = Category::where('id', $categoryId)->isActive()->first();
        return response()->json(Category::categoryChildrenJSON($category));

    }


}
