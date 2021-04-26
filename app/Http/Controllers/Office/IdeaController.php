<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;

use App\Page;
use App\Idea;
use App\User;


class IdeaController extends Controller
{
    /**
     * Show user ideas
     * @param  Request  $request
     * @return View
     */
    public function userIdeas(Request $request){

        $page = new Page();
        $page->title = __('');

        return view('office.ideas_list', [
            'page' => $page,
            'ideas' => Idea::itemsOfUser(User::activeUser()),
        ]);
    }

}
