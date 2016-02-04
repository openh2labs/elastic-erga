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
use App\LibratoUtil;
use Mail;
use Log;





class AlertController  extends BaseController {
    /*
     * @todo make echo statements displayed when in debug
     */

    private $alert_run;
    private $current_alert;
    

    /**
     * displays the alerts dashboard
     *
     */
    public function home($state="all"){
        $data = array();
        if($state == "all"){
            $alert = alerts::all();
            $data['title'] = "all setup";
        }elseif($state == "all_state"){ //all types in alert state
            $alert = alerts::AllState()->get();
            $data['title'] = "all active";
        }elseif($state == "pct_state"){ //all in pct alert state
            $alert = alerts::AllPct()->orderBy('created_at')->get();
            $data['title'] = "percentage active";
        }elseif($state == "hit_state"){ //in hit alert state
            $alert = alerts::AllHit()->orderBy('created_at')->get();
            $data['title'] = "hit active";
        }elseif($state == "zero_hit_state"){
            $data['title'] = "zero hit active";
            $alert = alerts::AllZeroHit()->orderBy('created_at')->get();
        }elseif($state == "es_config_error_state"){
            $data['title'] = "elastic search config state";
            $alert = alerts::AllESErrors()->orderBy('created_at')->get();
        }
        else{
            echo "error(1)";die;
        }
        $data['alerts'] = $alert;
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
            $this->current_alert->es_config_error_state = false;
            $this->current_alert->save();
            return $client->search($params2);

        }catch (\Exception $e){
            $this->current_alert->es_config_error_state = true;
            $this->current_alert->save();
            echo "<pre>error(searchELK)";
            print_r($e->getMessage());
            print_r($params2);
            echo "\n query: $query</pre>";
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
        echo "<br>From: ".date("Y-m-d H:i:s", strtotime('-'.$alert->minutes_back.' minutes'));
        echo "<br>To: ".date("Y-m-d H:i:s");
        $start_date = "".(date('U', strtotime('-'.$alert->minutes_back.' minutes'))*1000);
        $end_date = "".(date('U')*1000);
        $alert->criteria_temp = str_replace("%start_date%", $start_date, $alert->criteria);
        $alert->criteria_temp = str_replace("%end_date%", $end_date, $alert->criteria_temp);
        $alert->criteria_total_temp = str_replace("%start_date%", $start_date, $alert->criteria_total);
        $alert->criteria_total_temp = str_replace("%end_date%", $end_date, $alert->criteria_total_temp);
        return $alert;
    }


    /**
     * returns a string where the current year, month, day replace some holders
     * @param $string
     * @return mixed
     */
    private function getDateParams($string){
        $string = str_replace("%Y%", date('Y'), $string);
        $string = str_replace("%m%", date('m'), $string);
        $string = str_replace("%d%", date('d'), $string);
        return $string;
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

            //update librato
            $this->updateLibrato($hits_total, $hits, $alert->librato_id);

            //screen output
            echo "<br>".$hits_total." total hits";
            if($hits_total>0){
                echo "<br>".number_format((($hits/$hits_total)*100),2)."% hits";
                echo "<br>".number_format((($hits/$hits_total)*100),2)."% hits";
            }else{
                echo "<br>total hits are 0";
            }

            echo "<hr>";
        }
    }

    /**
     * updates librato dashboard
     * @param $metric
     * @param $value
     */
    function updateLibrato($metric_ok, $metric_alert, $librato_id){
        $librato = new LibratoUtil;
        //removing alert values as we are assuming that metric contains all responses

        $librato->push(($metric_ok-$metric_alert), $metric_alert, $librato_id);
    }


    /**
     * check if the alert conditions have been met and record the hits
     * @param $alert, the alert object
     * @param $hits, the absolute number of search hits
     * @param $total_hits, the total number of hits
     */
    function checkAlertCondition($alert, $hits, $total_hits){
        $librato = new LibratoUtil;
        //absolute hit number check (greater than zero check)
        if($alert->number_of_hits > 0 && $alert->number_of_hits < $hits && $alert->alert_type == 'gt0'){
            $this->alert_run->total_alerts_absolute = $this->alert_run->total_alerts_absolute + 1;
            $alert->number_hit_alert_state = true;
            //send email
            $this->sendMail($alert, $alert->description." exceeded ".$alert->number_of_hits." hits.");
            //add librato annotation
            $librato->pushAnnotation($alert->librato_id, "absolute-hit-alert-".$alert->description, "The number of hits for the search you are monitoring exceeded your threshold", "http://mytestlink.local", time(), time(), $alert->description);

            echo "<br>absolute hit threshold met";
        }else{
            $alert->number_hit_alert_state = false;
        }

        //percentage hit check (greater than zero check)
        if($total_hits>0){
            $alert_pct = (($hits/$total_hits)*100);
        }else{
            $alert_pct=0;
        }

        if($alert->pct_of_total_threshold > 0 && $alert->pct_of_total_threshold < $alert_pct && $alert->alert_type == 'gt0'){
            $this->alert_run->total_alerts_pct = $this->alert_run->total_alerts_pct + 1;
            $alert->pct_alert_state = true;
            $this->sendMail($alert, $alert->description." exceeded ".$alert->pct_of_total_threshold."%.");
            //add librato annotation
            $librato->pushAnnotation($alert->librato_id, "percentage-hit-alert-".$alert->description, "The percentage for the search you are monitoring exceeded your threshold", "http://mytestlink.local", time(), time(), $alert->description);
            echo "<br>hit pct threshold met";
        }else{
            $alert->pct_alert_state = false;
        }

        //alert hits equal zero
        if($hits == 0 && $alert->alert_type == 'e0'){
            $this->alert_run->total_alerts_equal_zero = $this->alert_run->total_alerts_equal_zero + 1;
            $alert->zero_hit_alert_state = true;
            $this->sendMail($alert, $alert->description." has $hits hits");
            $librato->pushAnnotation($alert->librato_id, "zero-hits-alert-".$alert->description, "The number of hits for the search you are monitoring is zero", "http://mytestlink.local", time(), time(), $alert->description);
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
            $message->from($alert->alert_email_sender, $alert->alert_email_sender);
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
        echo "\n***** running $query_type *****";
        try{
            $return_val = 0;
            $this->current_alert = $alert;
            if($query_type == "alert"){
                echo "\nprocessing alert";
                if($alert->criteria_temp != ""){
                    $params = json_decode($alert->criteria_temp,true);
                    $result = $this->searchELK($this->getDateParams($alert->es_index), $alert->es_type, array($alert->es_host), $params, array(), 'count');
                    $return_val = $result['hits']['total'];
                }else{
                    echo "\nNo valid search query found for monitor";
                }
            }elseif($query_type == "total"){
                if($alert->criteria_total_temp != ""){
                    $params = json_decode($alert->criteria_total_temp,true);
                    $result = $this->searchELK($this->getDateParams($alert->es_index), $alert->es_type, array($alert->es_host), $params, array(), 'count');
                    $return_val = $result['hits']['total'];
                }else{
                    echo "\nNo valid search query found for totals";
                }
            }
            echo "<br>index: ".$this->getDateParams($alert->es_index);
            echo "<br>index type: ".$alert->es_type;
            echo "<br>index host: ".$alert->es_host;
            return $return_val;
        }catch(\Exception $e){
            echo "\nerror doSearch: ".$e->getMessage();
            echo "\nalert criteria: ".$alert->criteria_temp;
            return 0;
        }
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

