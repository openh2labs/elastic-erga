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
use App\Post;
use App\AlertExecution;
use Mail;
use Log;





class AlertController  extends BaseController {
    /*
     * @todo make echo statements displayed when in debug
     */

    public $alert_run;
    

    public function home(){
        $alert = alerts::all();
        $data = array('name'=>'woohoo!!!', 'alerts'=>$alert);

        return view ("alerts_home", $data);
    }


    /**
     * display a list of all the search results that the system currently checks for
     * currently also runs the cron
     * @todo separate the cron out
     */
    public function searchtest(){
        Log::info('Starting Alert checks');

        $start_time = date('U');
        $this->alert_run = new AlertExecution();
        $this->alert_run->description = "check for alerts (searchtest)";
        $this->alert_run->save();

        // get alert checks
        $this->getResult();

        //save the job run
        //$this->alert_run->description = "check for alerts";
        $this->alert_run->duration = date('U') - $start_time;
        $this->alert_run->save();
        Log::info('completed Alert checks (searchtest)');
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
          // echo "<pre>"; print_r($params2);// die;
            return $client->search($params2);

        }catch (\Exception $e){
            echo "<pre>error(1)";print_r($e->getMessage());echo"</pre>";
            $result['hits']['total'] = 0;
            return $result;
        }
    }


    /**
     * get the time period we are checking for alerts
     * @param $alert
     * @return mixed
     */
    private function getTimePeriod($alert){
        $start_date = "".(date('U', strtotime('-'.$alert->minutes_back.' minutes'))*1000);
        $end_date = "".(date('U')*1000);
        $alert->criteria = str_replace("%start_date%", $start_date, $alert->criteria);
        $alert->criteria = str_replace("%end_date%", $end_date, $alert->criteria);
        $alert->criteria_total = str_replace("%start_date%", $start_date, $alert->criteria_total);
        $alert->criteria_total = str_replace("%end_date%", $end_date, $alert->criteria_total);
        return $alert;
    }


    /**
     * check all alerts
     */
    function getResult(){
        echo"<pre>";
        //get data
        $alerts = alerts::all();
        foreach($alerts as $alert){
            echo "<h2>".$alert->description."</h2>";

            //add time constraint
            $alert = $this->getTimePeriod($alert);

            // echo $alert->criteria."<br>";
            $hits = $this->doSearch($alert, "alert");
            echo "<br>".$hits." hits found ";//.$alert->criteria;

            //search for total documents so that percentages can be calculated
            $hits_total = $this->doSearch($alert, "total");

            //check if we should be alerted
            $this->checkAlertCondition($alert, $hits, $hits_total);

            //screen output
            echo "<br>".$hits_total." total hits";
            echo "<br>".number_format((($hits/$hits_total)*100),2)."% hits";
            echo "<br>".number_format((($hits/$hits_total)*100),2)."% hits";
            echo "<hr>";
        }
    }


    /**
     * check if the alert conditions have been met and record the hits
     * @param $alert, the alert object
     * @param $hits, the absolute number of search hits
     * @param $total_hits, the total number of hits
     */
    function checkAlertCondition($alert, $hits, $total_hits){
        //absolute hit number check (greater than zero check)
        if($alert->number_of_hits > 0 && $alert->number_of_hits < $hits && $alert->alert_type == 'gt0'){
            $this->alert_run->total_alerts_absolute = $this->alert_run->total_alerts_absolute + 1;
            $alert->number_hit_alert_state = true;
            $this->sendMail($alert, $alert->description." exceeded ".$alert->number_of_hits." hits.");
            echo "<br>absolute hit threshold met";
        }else{
            $alert->number_hit_alert_state = false;
        }

        //percentage hit check (greater than zero check)
        $alert_pct = (($hits/$total_hits)*100);
        if($alert->pct_of_total_threshold > 0 && $alert->pct_of_total_threshold < $alert_pct && $alert->alert_type == 'gt0'){
            $this->alert_run->total_alerts_pct = $this->alert_run->total_alerts_pct + 1;
            $alert->pct_alert_state = true;
            $this->sendMail($alert, $alert->description." exceeded ".$alert->pct_alert_state."%.");
            echo "<br>hit pct threshold met";
        }else{
            $alert->pct_alert_state = false;
        }

        //alert hits equal zero
        if($hits == 0 && $alert->alert_type == 'e0'){
            $this->alert_run->total_alerts_equal_zero = $this->alert_run->total_alerts_equal_zero + 1;
            $alert->zero_hit_alert_state = true;
            $this->sendMail($alert, $alert->description." has $hits hits");
            echo "<br>zero hits alert threshold met";
        }else{
            $alert->zero_hit_alert_state = false;
        }

        $alert->save();
    }


    /**
     * sends an email notification for a particular alert
     * @param $alert
     */
    function sendMail($alert, $alert_description){
        Mail::send('email_alert', ['recipient' => $alert->alert_email_recipient, 'description' => $alert_description], function($message) use ($alert)
        {
            $message->from('mp@h2labs.co.uk', 'Laravel');
            $message->to($alert->alert_email_recipient, $alert->alert_email_recipient)->subject('elastic-erga alert:'.$alert->description);
        });
    }


    /**
     * search for a particular alert condition
     * @param $alert, $alert eloquent object
     * @param $query_type, alert query, or total query
     * @return int total hits
     * @todo remove es_type from db as it doesn't get used anymore, the search json can apply a type filter
     */
    function doSearch($alert, $query_type){
        if($query_type == "alert"){
            $params = json_decode($alert->criteria,true);
         //   echo "<br>($query_type type) ".($alert->criteria);//echo"filter:";print_r($filter2);echo"<hr>";
        }elseif($query_type == "total"){
            $params = json_decode($alert->criteria_total,true);
         //   echo "<br>($query_type type) ".($alert->criteria_total);//echo"filter:";print_r($filter2);echo"<hr>";
        }

        $result = $this->searchELK($alert->es_index, $alert->es_type, array($alert->es_host), $params, array(), 'count');
        return $result['hits']['total'];
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
     * populate test data to ELK
     */
    function populateELKtestData(){

        $posts = Post::all();
        foreach($posts as $post){
            $this->addELKTestDoc($post);
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


}

