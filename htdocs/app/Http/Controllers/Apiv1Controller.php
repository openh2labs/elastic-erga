<?php

namespace App\Http\Controllers;

use App\Api\v1\Endpoints\Terminal;
use Illuminate\Http\Request;

use App\Http\Requests;

class ApiV1Controller extends Controller
{
    /**
     * @param Request $request
     * @param string $q
     * @return \Illuminate\Http\JsonResponse
     */
    public function terminalGet(Request $request, $q='')
    {
        return (new Terminal)->get($request, $q);
    }
}
