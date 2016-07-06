<?php

namespace App\Api\v1\Endpoints;


use App\Api\v1\Components\Mock;
use Illuminate\Http\Request;

class Terminal extends BaseEndpoint
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $q = $request->input('q');

        $ret = (new Mock())->generateMockData($q);

        return response()->json($ret);
    }
}