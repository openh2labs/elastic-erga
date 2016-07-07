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

        // uncomment to revert to mock data
        //$ret = (new Mock())->generateMockData($q);
        //return response()->json($ret);

        $util = new ElasticUtil();
        $match = '{
        "size" : 50,
        "query" : {
        "match" : { REPLACE }
    }
    }';

        $matchAll = '{
        "size" : 50,
        "query" : {
        "match_all" : { }
    }
    }';

        if (empty($q)) {
            $query = $matchAll;
        } else {
            $query = str_replace('REPLACE', '"_all":"'.$q.'"', $match);
        }

        $data = $util->getTerminalData(json_decode($query, true));

        foreach ($data['hits'] as $item) {
            $source = $item['_source'];

            // TODO: write an adapter for different data types
            if (! isset($source['syslog_message'])) {
                // filter out everything non syslog
                // TODO: move pre-feiltering to elastic query
                continue;
            }

            $syslogMessage = json_decode($source['syslog_message']);
            if ($syslogMessage === null) {
                // TODO: handle decodibng failure
                continue;
            }

            $message = [
                'message' => $syslogMessage,
            ];

            $ret[] = $message;
        }
        
        return response()->json($ret);
    }
}