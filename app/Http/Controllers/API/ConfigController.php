<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Classes\FrontConfig;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    private FrontConfig $config;

    public function __construct()
    {
        $this->config = new FrontConfig();
    }

    /**
     * Show the application dashboard.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(['config' => $this->config->getConfig(request()->has('section') ? request()->input('section') : null)]);
    }

}
