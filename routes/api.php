<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\IdeaController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1'], function() {

    Route::group(['prefix' => 'auth', 'namespace' => 'API'], function ($router) {

        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);

    });

    Route::group(['middleware' => ['auth:api']], function () {

        Route::get('/test', function (Request $request) {
            return ['result' => 'ok'];
        });




        // profiles
        Route::resource('profiles', ProfileController::class);


        Route::patch('/users/{user}/setNewPassword', [UserController::class, 'setNewPassword'])
            ->middleware(['auth','can:update,user'])
            ->name('api.profiles.set_new_password');

        //Get user ideas
        Route::get('/users/{user}/ideas', [UserController::class, 'ideas'])
            ->name('api.user.ideas');

        // ideas
        Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('api.ideas.edit');
        //Route::post('/ideas', 'API\IdeaController@store')->name('api.ideas.store');
        Route::patch('/ideas/{idea}/relationships/{relationship}', [IdeaController::class, 'updateRelationship'])->name('api.ideas.update_relationship');

        //Route::patch('/ideas/{idea}', 'API\IdeaController@update')->name('api.ideas.update');
        Route::patch('/ideas/{idea}/validate', [IdeaController::class, 'validateIdea'])->name('api.ideas.validate');
        //Route::delete('/ideas/{idea}', 'API\IdeaController@destroy')->name('api.ideas.destroy');

        Route::resource('ideas', IdeaController::class);

        // places
        Route::get('/places/create', 'API\PlaceController@create')->name('api.places.create');
        Route::get('/places/{place}/edit', 'API\PlaceController@edit')->name('api.places.edit');
        Route::post('/places', 'API\PlaceController@store')->name('api.places.store');
        Route::patch('/places/{place}', 'API\PlaceController@update')->name('api.places.update');
        Route::delete('/places/{place}', 'API\PlaceController@destroy')->name('api.places.destroy');

        // OpenStreetMap
        Route::get('/osm/{osm}/edit', 'API\OSMController@edit')->name('api.osm.edit');
        Route::post('/osm', 'API\OSMController@store')->name('api.osm.store');
        Route::patch('/osm/{place}', 'API\OSMController@update')->name('api.osm.update');
        Route::delete('/osm/{place}', 'API\OSMController@destroy')->name('api.osm.destroy');


        // categories
        Route::get('/categories/create', 'API\CategoryController@create')->name('api.category.create');
        Route::get('/categories/{category}/edit', 'API\CategoryController@edit')->name('api.category.edit');
        Route::post('/categories', 'API\CategoryController@store')->name('api.category.store');
        Route::patch('/categories/{category}', 'API\CategoryController@update')->name('api.category.update');
        Route::delete('/categories/{category}', 'API\CategoryController@destroy')->name('api.category.destroy');


        // tags
        Route::get('/tags/allMain', 'API\TagController@allMainTagsCollection')->name('api.tags.all_main_tags_collection');


        /*
         * -------------------------------------------------------------------------
         * ACTIONS PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/posts/{post}/photos', "API\Photo\ActionController@index")
            ->name('api.posts.photos_index');

        //Upload photo
        Route::post('/posts/{post}/photos', 'API\Photo\ActionController@upload')
            ->name('api.posts.photos_upload');

        //Set item main photos
        Route::patch('/posts/{post}/photos/{photo}/set_main', 'API\Photo\ActionController@setMain')
            ->name('api.posts.photos_set_main');

        //Delete item main photos
        Route::delete('/posts/{post}/photos/{photo}', 'API\Photo\ActionController@destroy')
            ->name('api.posts.photos_destroy');
        /*
         * -------------------------------------------------------------------------
         * ACTIONS PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/actions/{action}/photos', "API\Photo\ActionController@index")
            ->name('api.actions.photos_index');

        //Upload photo
        Route::post('/actions/{action}/photos', 'API\Photo\ActionController@upload')
            ->name('api.actions.photos_upload');

        //Set item main photos
        Route::patch('/actions/{action}/photos/{photo}/set_main', 'API\Photo\ActionController@setMain')
            ->name('api.actions.photos_set_main');

        //Delete item main photos
        Route::delete('/actions/{action}/photos/{photo}', 'API\Photo\ActionController@destroy')
            ->name('api.actions.photos_destroy');

        /*
         * -------------------------------------------------------------------------
         * IDEAS PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/ideas/{idea}/photos', "API\Photo\IdeaController@index")
            ->name('api.ideas.photos_index');

        //Upload photo
        Route::post('/ideas/{idea}/photos', 'API\Photo\IdeaController@upload')
            ->name('api.ideas.photos_upload');

        //Set item main photos
        Route::patch('/ideas/{idea}/photos/{photo}/set_main', 'API\Photo\IdeaController@setMain')
            ->name('api.ideas.photos_set_main');

        //Delete item main photos
        Route::delete('/ideas/{idea}/photos/{photo}', 'API\Photo\IdeaController@destroy')
            ->name('api.ideas.photos_destroy');

        Route::resource('users.ideas', 'API\UserIdeaController');

        /*
         * -------------------------------------------------------------------------
         * IDEA ITINERARY PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/itineraries/{itinerary}/photos', "API\Photo\ItineraryController@index")
            ->name('api.itineraries.photos_index');

        //Upload photo
        Route::post('/itineraries/{itinerary}/photos', 'API\Photo\ItineraryController@upload')
            ->name('api.itineraries.photos_upload');

        //Upload and set main photo
        Route::post('/itineraries/{itinerary}/photo', 'API\Photo\ItineraryController@uploadMain')
            ->name('api.itineraries.photos_upload');

        //Set item main photos
        Route::patch('/itineraries/{itinerary}/photos/{photo}/set_main', 'API\Photo\ItineraryController@setMain')
            ->name('api.itineraries.photos_set_main');

        //Delete item main photos
        Route::delete('/itineraries/{itinerary}/photos/{photo}', 'API\Photo\ItineraryController@destroy')
            ->name('api.itineraries.photos_destroy');

        /*
         * -------------------------------------------------------------------------
         * PLACES PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/places/{place}/photos', "API\Photo\PlaceController@index")
            ->name('api.ideas.photos_index');

        //Upload photo
        Route::post('/places/{place}/photos', 'API\Photo\PlaceController@upload')
            ->name('api.ideas.photos_upload');

        //Set item main photos
        Route::patch('/places/{place}/photos/{photo}/set_main', 'API\Photo\PlaceController@setMain')
            ->name('api.ideas.photos_set_main');

        //Delete item main photos
        Route::delete('/places/{place}/photos/{photo}', 'API\Photo\PlaceController@destroy')
            ->name('api.ideas.photos_destroy');


        /*
         * -------------------------------------------------------------------------
         * PROFILES PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/profiles/{profile}/photos', "API\Photo\ProfileController@index")
            ->name('api.profiles.photos_index');

        //Upload photo
        Route::post('/profiles/{profile}/photos', 'API\Photo\ProfileController@upload')
            ->name('api.profiles.photos_upload');

        //Set item main photos
        Route::patch('/profiles/{profile}/photos/{photo}/set_main', 'API\Photo\ProfileController@setMain')
            ->name('api.profiles.photos_set_main');

        //Delete item main photos
        Route::delete('/profiles/{profile}/photos/{photo}', 'API\Photo\ProfileController@destroy')
            ->name('api.profiles.photos_destroy');

        // User
        Route::resource('users', 'API\UserController');

        // Profile
        Route::resource('profiles', 'API\ProfileController');

    });

    /*
     * --------------------------------------------------------------------------
     * PLACES
     * --------------------------------------------------------------------------
     */

    //Get listing of places by title
    Route::get('/places/getByTitle', "API\PlaceController@getByTitle")
        ->name('api.place.get_by_title');

    //Get listing of regions and cities by title
    Route::get('/places/getRegionOrCityByTitle', "API\PlaceController@getRegionOrCityByTitle")
        ->name('api.place.get_region_or_city_by_title');

    //Get listing of places by title
    Route::get('/ideas/getByTitle', "API\IdeaController@getByTitle")
        ->name('api.idea.get_by_title');

    //Get listing of places by title
    Route::get('/ideas/main', "API\IdeaController@getMain")
        ->name('api.idea.main');

    Route::get('/ideas/randomIdea', 'API\IdeaController@randomIdea')->name('api.ideas.random_idea');

    // filters

    Route::get('/filters/{filter}/activeItems', "API\FilterController@activeItems")
        ->name('api.filters.active_items');

    // currencies
    Route::get('/currencies', "API\CurrencyController@index")
        ->name('api.currencies.index');

    // OpenStreetMap
    Route::get('/osm/search', "API\OSMController@search")->name('api.osm.search');
    Route::get('/osm/{osm}', "API\OSMController@view")->name('api.osm.view');
    Route::post('/osm/saveSelected', 'API\OSMController@saveSelected')->name('api.osm.store');

    // Ideas

    Route::get('ideas', [IdeaController::class, 'index'])->name('api.ideas');
    Route::get('/ideas/{idea}', 'API\IdeaController@show')->name('api.ideas.show');

    // Idea itinerary
    Route::resource('ideas.itineraries', 'API\IdeaItineraryController');

    // Idea date
    Route::resource('ideas.dates', 'API\IdeaDateController');

    // Categories
    Route::get('/categories', 'API\CategoryController@index')->name('api.category.index');
    Route::get('/categories/children', 'API\CategoryController@lastChildren')->name('api.category.last_children');
    Route::get('/categories/{category}', 'API\CategoryController@show')->name('api.category.show');
    Route::get('/categories/{categoryId}/fullcategorieslisting', 'Api\CategoryController@fullCategoriesListing')->name('api.category.fullcategorieslisting');
    Route::get('/categories/{categoryId}/child', 'API\CategoryController@child')->name('api.category.child');

});
