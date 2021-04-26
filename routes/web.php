<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('ideas/{categories?}', 'IdeaController@index')
    ->where('categories', '^[a-zA-Z0-9-_\/]+$')
    ->name('ideas.index');
Route::get('ideas/{categories}/-{idea}', 'IdeaController@show')
    ->where(['categories' => '^[a-zA-Z0-9-_\/]+$', 'idea' => '^[a-zA-Z0-9-_\/]+$'])
    ->name('ideas.show');

Route::get('/{any?}', function($any=''){
    return view('index');
})->where('any', '.*');
