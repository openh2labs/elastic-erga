<?php

namespace App\Http\Controllers;

/**
 * Class SearchController.
 *
 * 1. Proxy Elastic Search get methods to protect it from write access
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
        return ['meta' => [], 'data' => []];
    }
}
