<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 22/01/16
 * Time: 08:38
 */

namespace App;

use Elasticsearch\ClientBuilder;

// ADD use ElasticSearchClassesHere

class ElasticUtil {


    /**
     * returns a string where the current year, month, day replace some holders
     * @param $string
     * @return mixed
     */
    public function getDateValues($string){
        $string = str_replace("%Y%", date('Y'), $string);
        $string = str_replace("%m%", date('m'), $string);
        $string = str_replace("%d%", date('d'), $string);
        return $string;
    }



    /**
     * searches ELK for a doc
     * if search_type = count then it will only return aggregation results
     * if fields array is empty it will return all fields
     */
    public function searchELK($index, $index_type, $host, $query, $fields, $search_type){
        try{

            $client = ClientBuilder::create()->setRetries(0)->setHosts($host)->build();
            $params2['index'] = $index;
            $params2['client'] = ['timeout'=>5, 'connect_timeout'=>1];
            $params2['body'] = $query;

            if($index_type != ""){
                $params2['type'] = $index_type;
            }

            if(count($fields)>0){
                $params2['body']['fields'] = $fields;
            }
            if($search_type != ""){
                $params2['search_type'] = $search_type;
            }
            return $client->search($params2);

        }catch (\Exception $e){
            echo "<pre>error(1)";print_r($e->getMessage());echo"</pre>";
            $result['hits']['total'] = 0;
            return $result;
        }
    }


    /**
     * create the test index
     * @param $host, the ES host
     */
    function createIndex(){
        try{
            // $client = ClientBuilder::create()->build();
            $client = ClientBuilder::create()->setHosts(["192.168.10.10"])->build();
            $params = [
                'index' => 'default_v5',
                'body' => [
                    'settings' => [
                        'number_of_shards' => 5,
                        'number_of_replicas' => 1
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
     * adds a single test doc to the test index
     * @param $post
     */
    function addELKTestDoc($post){
        try{
            $client = ClientBuilder::create()->setHosts(['192.168.10.10'])->build();
            $params = [
                'index' => 'default_v5',
                'type' => 'posts_v5',
                'id' => $post->id,
                //  'timestamp' => strtotime("".$post->updated_at)*1000,
                'body' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'tags' => $post->tags,
                    'created_at' => "".$post->created_at,
                    'updated_at' => "".$post->updated_at,
                    //   '_timestamp' => strtotime("".$post->updated_at)
                ]
            ];

            $response = $client->index($params);
            print_r($response);
        }catch(\Exception $e){
            echo "<pre>";
            print_r($post);
            echo $e->getMessage();
        }
    }

    /**
     * creates the test mappings
     */
    function addTestMappings(){
        echo "<br>adding mappings";
        try{
            $client = ClientBuilder::create()->setHosts(["192.168.10.10"])->build();
            $params = [
                'index' => 'default_v5',
                'type' => 'posts_v5',
                'body' => [
                    'posts_v5' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'content' => [
                                'type' => 'string'
                            ],
                            'created_at' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss' // yyyy/MM/dd HH:mm:ss //yyyy-MM-dd HH:mm:ss
                            ],
                            'updated_at' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss' //yyyy-MM-dd HH:mm:ss
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
                            /*

                            */
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

        //add test data
        $this->populateELKtestData();
    }

    /**
     * returns data used in the terminal
     */
    function getTerminal(){
        $query = json_decode($this->someTestData(),true);
        //$index, $index_type, $host, $query, $fields, $search_type
        $t = $this->searchELK("web_logs-2016-03-16", "nginx", array("10.0.22.71:9200"), $query, array(), "");
        //  if(count($data['hits']['hits'])>0){
        //echo "<pre>";print_r($data['hits']['hits']);
        //echo(json_encode($data['hits']['hits']));
        //  }
        $data['indices'] = array();
        $data['hits'] = $t['hits']['hits'];
        return $data;
    }

    //temp sample data @todo remove
    private function someTestData(){
        return '{
        "size" : 50,
        "query" : {
        "match_all" : {}
    }
    }';
    }


}