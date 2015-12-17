<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/10/15
 * Time: 21:44
 */

namespace App\Http\Controllers;


use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\alerts;





class AlertController  extends BaseController {

    public function home(){
        $alert = alerts::all();
        $data = array('name'=>'woohoo!!!', 'alerts'=>$alert);

        return view ("alerts_home", $data);
    }

    public function searchtest(){

        // get alert checks


        $this->getResult();

    }

    function getResult(){
        try{
            // $elasticsearch = new elasticsearch/elasticsearch();
            //'10.0.2.15'
            //http://192.168.0.250:9200

            $client = ClientBuilder::create()->setHosts(['192.168.10.10'])->build();
            $params = [
                'index' => 'default',
                'type' => 'user_registration_legacy',
                'body' => [
                    'query' => [
                        'match' => [
                            'event' => 'user_registration'
                        ]
                    ]
                ]
            ];

            $result = $client->search($params);
            $response['result'] = "ok";
            $response['result_code'] = 200;
            $response['result_hits'] = $result['hits']['total'];
            $response['result_body'] = $result;

            echo "<pre>";
            print_r($response);
            echo "</pre>";

        }catch(\Exception $e){
            echo "<pre>";
            $var = json_decode($e->getMessage(),true);
            print_r($this->getESException($var));
            echo "</pre>";
        }

        //echo "<pre>";
        //print_r($response);
    }

    function getESException($error){
        $result['result_hits'] = 0;
        $result['result_code'] = $error['status'];
        $result['result'] = "error";
        $result['result_body'] = $error;
        return $result;
    }
}

