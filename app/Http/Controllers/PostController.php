<?php

namespace App\Http\Controllers;

use App\Post;
use App\Page;
use App\PostCategory;
use App\Http\Requests\Post\StorePost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->idea = $post;
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
        $page->title = __('post.title');

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
        }

        $categories = PostCategory::getCategoriesForSelector($activeCategory);
        $posts = Post::itemsList($request, $activeCategory);

        return view('post.index', compact([
            'page', 'posts', 'activeCategory', 'categories', 'categoriesUrl', 'subCategory'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Post $post)
    {
        return view('post.edit' , ['post']);
    }



    /**
     * Display the specified resource.
     *
     * @param  PostCategory $category
     * @param  String $itemSlug
     * @return \Illuminate\Http\Response
     */
    public function show( $category, $itemSlug)
    {

        $item = Post::where('slug', $itemSlug)->first();
        $ideaActions = $item->actionItemsList(4);

        if(!$item){
            abort('404');
        }

        /*$breadcrumb_array = [
            ['title' => 'Главная', 'url' => route('home',  session('current_city_alias'))],
            ['title' => 'Товары', 'url' => route('products.list',[session('current_city_alias'),''])],
            ['title' => 'Новый товар',  'url' => '#'],
        ];*/

        return view('idea.show' , compact(['item', 'category', 'ideaActions', 'breadcrumb_array' ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('post.edit',  compact(['post']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StorePost $request
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(StorePost $request, Post $post)
    {

        $updateResult = $post->updateAndSync($request);

        if($updateResult){
            // очищаем старый кэш
            //Cache::forget('category-all');
            //Cache::forget('category-widget');

            return redirect()->back()->with('status', __('post.updated'));

        }else{
            return redirect()->back()->with('status', __('post.not_updated'));

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
