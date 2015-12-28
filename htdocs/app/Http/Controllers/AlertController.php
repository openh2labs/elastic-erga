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

    /**
     * @param string $start_dt, mysql start date
     * @param string $end_dt, mysql end date
     * @return mixed
     */
    function getFieldList($start_dt, $end_dt){
        $fieldsArr = array("field_name_1", "field_name_3", "field_name_4");
        $filter = array();
        // $filter ['bool']['must'][]['term']['game_id'] = $game_id;
        $filter ['bool']['must'][]['term']['_type'] = $this->index_type;
        $filter ['bool']['must'][]['range']['game_date']['gt'] = $start_dt;
        $filter ['bool']['must'][]['range']['game_date']['lte'] = $end_dt;
        /*

        $filter ['bool']['must'][]['term']['reported'] = "0";
        $filter ['bool']['must'][]['term']['publishable'] = "1";
        $filter ['bool']['must'][]['term']['completed'] = "1";
        $filter ['bool']['must'][]['term']['enabled'] = "1";
        $filter ['bool']['must'][]['range']['created_at']['gt'] = "$year-$month-01 00:00:00";
        $filter ['bool']['must'][]['range']['created_at']['lte'] = "$eom 23:59:59";
        */
        $query = array();
        $query['match']['_type'] = $this->index_type;

        $params['query']['filtered'] = array(
            "filter" => $filter,
            "query"  => $query
        );

        //aggregate the results
        $params['aggs']['field_name_2']['terms']['field'] = 'field_name_3';
        $params['aggs']['field_name_2']['terms']['size'] = 50000;
        $params['aggs']['field_name_2']['aggs']['field_name_4']['terms']['field'] = 'field_name_1';
        $params['aggs']['field_name_2']['aggs']['field_name_4']['terms']['size'] =  50000;
        $params['aggs']['field_name_2']['aggs']['field_name_4']['aggs']['game_id']['terms']['field'] = 'game_id';
        $params['aggs']['field_name_2']['aggs']['field_name_4']['aggs']['game_id']['terms']['size'] = 50000;

        $elkdoc = searchELK($this->index, $this->index_type, $this->host, $params, $fieldsArr, "_count");

        $result = array();
        if($elkdoc['hits']['total'] > 0){
            $result['response'] = "200 OK";
            $result['status'] = "200";
        }else{
            $result['response'] = "404 not found";
            $result['status'] = "404";
        }
        $result['body'] = $elkdoc['aggregations']['field_name_2']['buckets'];
        return $result;
    }


    /**
     * searches ELK for a doc
     * if search_type = count then it will only return aggregation results
     * if fields array is empty it will return all fields
     */
    private function searchELK($index, $index_type, $host, $query, $fields, $search_type){
        try{
           // $client = ClientBuilder::create()->setHosts(['192.168.10.10'])->build();
            $params = array();
            $params['hosts'] = array (
                $host         // IP + Port
            );
           // $client = new \Elasticsearch\Client($params);
            $client = ClientBuilder::create()->setHosts($host)->build();
            $params2['index'] = $index;
            if($index_type != ""){
                $params2['type'] = $index_type;
            }


            $params2['body'] = $query;
            if(count($fields)>0){
                $params2['body']['fields'] = $fields;
            }
            if($search_type != ""){
                $params2['search_type'] = $search_type;
            }
            //   echo "<pre>"; print_r($params2); die;
            return $client->search($params2);

        }catch (Exception $e){
            $result['hits']['total'] = 0;
            return $result;
        }
    }

    function getResult(){
        echo"<pre>";
        //get data
        $alerts = alerts::all();
      //  $data = array('name'=>'woohoo!!!', 'alerts'=>$alert);

        foreach($alerts as $alert){
            //add time constraint
            $alert->criteria = str_replace("%minutes%", "".$alert->minutes_back."m", $alert->criteria);

            // echo $alert->criteria."<br>";
            $hits = $this->doSearch($alert);
            echo "<br>".$hits." times found ";//.$alert->criteria;

            //search for documents without it
            $alert->criteria = str_replace("\"must\":", "\"must_not\":", $alert->criteria);
            // echo "<br>".$alert->criteria;die;
            $hits = $this->doSearch($alert);
            echo "<br>".$hits." times not found<hr> ";//.$alert->criteria;echo "<hr>";
        }


    }

    /**
     * search for a particular alert condition
     * @param $alert, $alert eloquent object
     * @return int total hits
     */
    function doSearch($alert){
        $filter2 = json_decode($alert->criteria);
        /*
        $filter ['bool']['must'][]['term']['_type'] = 'posts';
        $filter ['bool']['must'][]['term']['content'] = 'facere';
        //   $filter ['bool']['must'][]['range']['game_date']['gt'] = $start_dt;
        //   $filter ['bool']['must'][]['range']['game_date']['lte'] = $end_dt;
        */
        $query = array();
        $query['match']['_type'] = 'posts';

        $params['query']['filtered'] = array(
            "filter" => $filter2,
            "query"  => $query
        );
        //$params['aggs']['field_name_2']['terms']['field'] = 'id';




        // var_dump($data);
        echo "<br> ".($alert->criteria);
        $result = $this->searchELK($alert->es_index, $alert->es_type, array($alert->es_host), $params, array(), 'count');
       // print_r($result);
        //echo "<hr>";
        return $result['hits']['total'];
    }

    /**
     * deprecated
     */
    function getResultOld(){
        try{
            // $elasticsearch = new elasticsearch/elasticsearch();
            //'10.0.2.15'
            //http://192.168.0.250:9200

            $client = ClientBuilder::create()->setHosts(['192.168.10.10'])->build();
            $params['index'] = 'default';
            $params['type'] = 'posts';
            $params['body']['aggs'] = array();


            $filters = array();
            $filters['bool']['should'][]['term']['content'] = 'facere';
            $params['body']['query']['filtered'] = array('filters'=>$filters);

            /*
            $params = [
                'index' => 'default',
                'type' => 'posts',
                'body' => [
                    'query' => [
                        'match' => [
                            'content' => 'facere'
                        ]
                    ]

                ],
                    'fields' => ''

            ];
            */

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

    /**
     * returns elastic search exception
     * @param $error
     * @return mixed
     */
    function getESException($error){
        $result['result_hits'] = 0;
        $result['result_code'] = $error['status'];
        $result['result'] = "error";
        $result['result_body'] = $error;
        return $result;
    }

    /**
     * create the test index
     * @param $host, the ES host
     */
    function createTestIndex(){
        try{
            // $client = ClientBuilder::create()->build();
            $client = ClientBuilder::create()->setHosts(["192.168.10.10"])->build();
            $params = [
                'index' => 'default_v2',
                'body' => [
                    'settings' => [
                        'number_of_shards' => 3,
                        'number_of_replicas' => 2
                    ]
                ]
            ];

            //add the mappings 
            $this->addTestMappings();
            // Create the index with mappings and settings now
            $response = $client->indices()->create($params);
        }catch(\Exception $e){
            echo"<pre>";
            echo "<h1>createTestIndex error</h1>";
            print_r($e->getMessage());
            //if index exists lets add the mappings
            $this->addTestMappings();

        }


    }

    /**
     * creates the test mappings
     */
    function addTestMappings(){
        try{
            $client = ClientBuilder::create()->setHosts(["192.168.10.10"])->build();

            /*
            $params = [
                'index' => 'default',
                'body' => [
                    'settings' => [
                        'number_of_shards' => 3,
                        'number_of_replicas' => 2
                    ],
                    'mappings' => [
                        'posts_v2' => [
                            '_source' => [
                                'enabled' => true,
                                'properties' => [
                                    'content' => [
                                        'type' => 'string',
                                        'analyzer' => 'standard'
                                    ],
                                    'created_at' => [
                                        'type' => 'date'
                                    ],
                                    'id' => [
                                        'type' => 'long'
                                    ],
                                    'tags' => [
                                        'type' => 'string'
                                    ],
                                    'title' => [
                                        'type' => 'string'
                                    ],
                                    'update_at' => [
                                        'type' => 'date'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            */
            $params = [
                'index' => 'default_v2',
                'type' => 'posts_v2',
                'body' => [
                    'posts_v2' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'content' => [
                                'type' => 'string',
                                'analyzer' => 'standard'
                            ],
                            'created_at' => [
                                'type' => 'date'
                            ],
                            'id' => [
                                'type' => 'long'
                            ],
                            'tags' => [
                                'type' => 'string'
                            ],
                            'title' => [
                                'type' => 'string'
                            ],
                            'update_at' => [
                                'type' => 'date'
                            ]
                        ]
                    ]
                ]
            ];

            // Update the index mapping
            $client->indices()->putMapping($params);
        }catch(\Exception $e){
            echo "<pre>";
            echo "<h1>addTestMappings error</h1>";
            print_r($e->getMessage());
        }

    }


}

