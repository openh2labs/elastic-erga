<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 22/01/16
 * Time: 08:38
 */

namespace App;

use Elasticsearch\ClientBuilder;
use App\Repositories\AlertRepository;

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
     *
     * returns an updated array with the date values injected
     *
     * @param $arr, array of strings
     */
    private function getDateValuesForArray($arr){
        foreach($arr as $key=>$value){
            $value->es_index = $this->getDateValues($value->es_index);
            $arr[$key] = $value;
        }
        return $arr;
    }

    /**
     * searches ELK for a doc
     * if search_type = count then it will only return aggregation results
     * if fields array is empty it will return all fields
     */
    public function searchELK($index, $index_type, $host, $query, $fields, $search_type){
        try{

           // echo"<pre>";print_r($index);print_r($host);die;
            //check if we need to search across indices
            if(is_array($index)){
                $index = implode($index, ",");
            }

           // echo"$index";die;
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
            echo "<pre>error(1:searchELK)";print_r($e->getMessage());echo"</pre>";
            $result['hits']['total'] = 0;
            $result['es_error'] = true;
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
     * @todo return minus offset
     */
    public function getTerminalData(){
        $ar = new AlertRepository();
        $indices = $this->getDateValuesForArray($this->getValidIndices($ar->getAllIndices()));
        $query = json_decode($this->someTestData(),true);
        $data = array();
        $data['meta']['total'] = 0;
        $data['meta']['total_hits_returned'] = 0;
        $data['meta']['event_timestamp_max'] = "";
        $data['meta']['event_timestamp_min'] = "";
        $data['indices'] = $this->getAllIndices($indices);
        $data['hosts'] = $this->getAllHosts($indices);
        $data['hits'] = array();
        foreach($indices as $key=>$val){
            $indices_arr = $this->getAllIndicesForHost($indices,$val->es_host);
            $t = $this->searchELK($indices_arr, "", array($val->es_host), $query, array(), "");
            if($t['hits']['total']>0){
                $data['hits'] = array_merge($data['hits'], $t['hits']['hits']);
            }
            $data['meta']['total'] = $data['meta']['total'] + $t['hits']['total'];
            $data['meta']['total_hits_returned'] = $data['meta']['total_hits_returned'] + 50;
        }
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

    /**
     *
     * returns an array of indices that can be found
     *
     * @param $indexArray
     * @return array of indices that the application can connect to
     */
    private function getValidIndices($indexArray){
        $indices = array();
        foreach($indexArray as $value){
            if($this->indexExists($value->es_host, $value->es_index)==true){
                $indices[] = $value;
            }
        }
        return $indices;
    }

    /**
     *
     * checks if an index exists
     *
     * @param string $host
     * @param $index
     * @return boolean , true if index exists
     */
    public function indexExists($host, $index){
        try{
            $index = $this->getDateValues($index);
            $client = ClientBuilder::create()->setRetries(0)->setHosts(array($host))->build();
            $indexParams['index'][]  = $index;
            return $client->indices()->exists($indexParams);
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     *
     * returns an array of all indices for a given host
     *
     * @param $indicesArr
     * @return array
     */
    private function getAllIndicesForHost($indicesArr, $host){
        $result = array();
        foreach($indicesArr as $key=>$value){
            $result[$value->es_host][] = $value->es_index;
        }
      //  echo "<pre>";print_r($result);die;
        return $result[$host];
    }

    /**
     *
     * returns an array of es_hosts
     *
     * @param $indicesArr
     * @return array
     */
    private function getAllHosts($indicesArr){
        $result = array();
       // echo"<pre>";print_r($indicesArr);die;
        foreach($indicesArr as $key=>$value){
            $result[] = $value->es_host;
        }
        return $result;
    }

    /**
     *
     * returns all indices
     *
     * @param $indicesArr
     * @return array
     */
    private function getAllIndices($indicesArr){
        $result = array();
        foreach($indicesArr as $key=>$value){
            $result[] = $value->es_index;
        }
        return $result;
    }

}