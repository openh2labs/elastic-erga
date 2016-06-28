<?php

namespace App\Api\v1\Endpoints;


use App\Api\v1\Components\Mock;
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
        $ret = (new Mock())->generateMockData();

        return response()->json($ret);
    }
}