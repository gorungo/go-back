<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CurrencyController;
use App\Http\Controllers\API\FilterController;
use App\Http\Controllers\API\IdeaController;
use App\Http\Controllers\API\IdeaDateController;
use App\Http\Controllers\API\IdeaItineraryController;
use App\Http\Controllers\API\OSMController;
use App\Http\Controllers\API\PlaceController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\Photo\IdeaController as PhotoIdeaController;
use App\Http\Controllers\API\Photo\ProfileController as PhotoProfileController;
use App\Http\Controllers\API\Photo\PlaceController as PhotoPlaceController;
use App\Http\Controllers\API\Photo\ItineraryController as PhotoItineraryController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserIdeaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


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

    Route::get('/storage-info', function(){
        dd( Storage:: allFiles(request()->input('folder')) );
    });
    Route::get('/asset', function(){
        echo Storage::url((request()->input('path')));
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

        Route::get('/tags/allMain', [TagController::class, 'allMainTagsCollection'])->name('api.tags.all_main_tags_collection');

        /*
         * -------------------------------------------------------------------------
         * IDEAS PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/ideas/{idea}/photos', [PhotoIdeaController::class, 'index'])
            ->name('api.ideas.photos_index');

        //Upload photo
        Route::post('/ideas/{idea}/photos', [PhotoIdeaController::class, 'upload'])
            ->name('api.ideas.photos_upload');

        //Set item main photos
        Route::patch('/ideas/{idea}/photos/{photo}/set_main', [PhotoIdeaController::class, 'setMain'])
            ->name('api.ideas.photos_set_main');

        //Delete item main photos
        Route::delete('/ideas/{idea}/photos/{photo}', [PhotoIdeaController::class, 'destroy'])
            ->name('api.ideas.photos_destroy');

        Route::resource('users.ideas', UserIdeaController::class);

        /*
         * -------------------------------------------------------------------------
         * IDEA ITINERARY PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/itineraries/{itinerary}/photos', [PhotoItineraryController::class, 'index'])
            ->name('api.itineraries.photos_index');

        //Upload photo
        Route::post('/itineraries/{itinerary}/photos', [PhotoItineraryController::class, 'upload'])
            ->name('api.itineraries.photos_upload');

        //Upload and set main photo
        Route::post('/itineraries/{itinerary}/photo', [PhotoItineraryController::class, 'uploadMain'])
            ->name('api.itineraries.photos_upload');

        //Set item main photos
        Route::patch('/itineraries/{itinerary}/photos/{photo}/set_main', [PhotoItineraryController::class, 'setMain'])
            ->name('api.itineraries.photos_set_main');

        //Delete item main photos
        Route::delete('/itineraries/{itinerary}/photos/{photo}', [PhotoItineraryController::class, 'destroy'])
            ->name('api.itineraries.photos_destroy');

        /*
         * -------------------------------------------------------------------------
         * PLACES PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/places/{place}/photos', [PhotoPlaceController::class, 'index'])
            ->name('api.ideas.photos_index');

        //Upload photo
        Route::post('/places/{place}/photos', [PhotoPlaceController::class, 'upload'])
            ->name('api.ideas.photos_upload');

        //Set item main photos
        Route::patch('/places/{place}/photos/{photo}/set_main', [PhotoPlaceController::class, 'setMain'])
            ->name('api.ideas.photos_set_main');

        //Delete item main photos
        Route::delete('/places/{place}/photos/{photo}', [PhotoPlaceController::class, 'destroy'])
            ->name('api.ideas.photos_destroy');


        /*
         * -------------------------------------------------------------------------
         * PROFILES PHOTOS ROUTING
         * -------------------------------------------------------------------------
         */

        //Get listing of photos
        Route::get('/profiles/{profile}/photos', [PhotoProfileController::class, 'index'])
            ->name('api.profiles.photos_index');

        //Upload photo
        Route::post('/profiles/{profile}/photos', [PhotoProfileController::class, 'upload'])
            ->name('api.profiles.photos_upload');

        //Set item main photos
        Route::patch('/profiles/{profile}/photos/{photo}/set_main', [PhotoProfileController::class, 'setMain'])
            ->name('api.profiles.photos_set_main');

        //Delete item main photos
        Route::delete('/profiles/{profile}/photos/{photo}', [PhotoProfileController::class, 'destroy'])
            ->name('api.profiles.photos_destroy');

        // User
        Route::resource('users', UserController::class);

        // Profile
        Route::resource('profiles', ProfileController::class);

    });

    /*
     * --------------------------------------------------------------------------
     * PLACES
     * --------------------------------------------------------------------------
     */

    //Get listing of places by title
    Route::get('/places/getByTitle', [PlaceController::class, 'getByTitle'])
        ->name('api.place.get_by_title');

    //Get listing of regions and cities by title
    Route::get('/places/getRegionOrCityByTitle', [PlaceController::class, 'getRegionOrCityByTitle'])
        ->name('api.place.get_region_or_city_by_title');

    //Get listing of places by title
    Route::get('/ideas/getByTitle', [IdeaController::class, 'getByTitle'])
        ->name('api.idea.get_by_title');

    //Get listing of places by title
    Route::get('/ideas/main', [IdeaController::class, 'getMain'])
        ->name('api.idea.main');

    Route::get('/ideas/randomIdea', [IdeaController::class, 'randomIdea'])->name('api.ideas.random_idea');

    // filters

    Route::get('/filters/{filter}/activeItems', [FilterController::class, 'activeItems'])
        ->name('api.filters.active_items');

    // currencies
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('api.currencies.index');

    // OpenStreetMap
    Route::get('/osm/search', [OSMController::class, 'search'])->name('api.osm.search');
    Route::get('/osm/{osm}', [OSMController::class, 'view'])->name('api.osm.view');
    Route::post('/osm/saveSelected', [OSMController::class, 'saveSelected'])->name('api.osm.store');

    Route::resource('ideas', IdeaController::class);
    Route::resource('places', PlaceController::class);
    Route::resource('osm', OsmController::class);
    Route::resource('categories', CategoryController::class);

    // Ideas

    Route::get('ideas', [IdeaController::class, 'index'])->name('api.ideas');
    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('api.ideas.show');

    // Idea itinerary
    Route::resource('ideas.itineraries', IdeaItineraryController::class);

    // Idea date
    Route::resource('ideas.dates', IdeaDateController::class);

    // Categories
    Route::get('/categories', 'API\CategoryController@index')->name('api.category.index');
    Route::get('/categories/children', 'API\CategoryController@lastChildren')->name('api.category.last_children');
    Route::get('/categories/{category}', 'API\CategoryController@show')->name('api.category.show');
    Route::get('/categories/{categoryId}/fullcategorieslisting', 'Api\CategoryController@fullCategoriesListing')->name('api.category.fullcategorieslisting');
    Route::get('/categories/{categoryId}/child', 'API\CategoryController@child')->name('api.category.child');

});
