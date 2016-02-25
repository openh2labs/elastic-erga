<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class SearchController
 *
 * 1. Proxy Elastic Search get methods to protect it from write access
 *
 * @package App\Http\Controllers
 */
class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function elastic()
    {
        return ['meta' => [], 'data' => [] ];
    }

}
