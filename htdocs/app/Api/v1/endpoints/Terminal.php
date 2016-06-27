<?php

namespace App\Api\v1\Endpoints;


use Illuminate\Http\Request;

class Terminal extends BaseEndpoint
{
    /**
     * @param Request $request
     * @param string $q
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $q='')
    {
        $ret = ['key' => 'val'];
        return response()->json($ret);
    }
}