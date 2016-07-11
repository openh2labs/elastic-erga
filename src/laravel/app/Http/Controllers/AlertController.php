<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/10/15
 * Time: 21:44
 */

namespace App\Http\Controllers;


use App\Api\v1\Components\AlertMailer;
use App\ElasticUtil;
use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Alert;
use App\Post;
use App\AlertExecution;
use App\LibratoUtil;
use Mail;
use Log;


class AlertController extends BaseController
{

    /*
     * @todo make echo statements displayed when in debug
     * @todo move alert checking logic in a repository
     *
     */

    private $alert_run;
    private $current_alert;
    private $alert_total_checks; //total alert checks
    private $eu; //elastic util helper

    public function __construct()
    {
        $this->alert_total_checks = 0;
        $this->eu = new ElasticUtil();
    }

    /**
     * displays the alerts dashboard
     *
     */
    public function home($state = "all")
    {
        //$l = new LibratoUtil;
        $data = array();
        if ($state == "all") {
            $alert = Alert::all();
            $data['title'] = "all setup";
        } elseif ($state == "all_state") { //all types in alert state
            $alert = Alert::AllState()->get();
            $data['title'] = "all active";
        } elseif ($state == "pct_state") { //all in pct alert state
            $alert = Alert::AllPct()->orderBy('created_at')->get();
            $data['title'] = "percentage active";
        } elseif ($state == "hit_state") { //in hit alert state
            $alert = Alert::AllHit()->orderBy('created_at')->get();
            $data['title'] = "hit active";
        } elseif ($state == "zero_hit_state") {
            $data['title'] = "zero hit active";
            $alert = Alert::AllZeroHit()->orderBy('created_at')->get();
        } elseif ($state == "es_config_error_state") {
            $data['title'] = "elastic search config state";
            $alert = Alert::AllESErrors()->orderBy('created_at')->get();
        } else {
            echo "error(1)";
            die;
        }

        $data['alerts'] = $alert;
        return view("alerts_home", $data);
    }

    /**
     * display a list of all the search results that the system currently checks for
     * currently also runs the cron
     * @todo separate the cron out
     */
    public function searchtest()
    {
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
    private function searchELK($index, $index_type, $host, $query, $fields, $search_type)
    {
        try {
            $client = ClientBuilder::create()->setRetries(0)->setHosts($host)->build();
            $params2['index'] = $index;
            $params2['client'] = ['timeout' => 5, 'connect_timeout' => 1];
            $params2['body'] = $query;

            if ($index_type != "") {
                $params2['type'] = $index_type;
            }

            if (count($fields) > 0) {
                $params2['body']['fields'] = $fields;
            }
            if ($search_type != "") {
                $params2['search_type'] = $search_type;
            }
            $this->current_alert->es_config_error_state = false;
            $this->current_alert->save();
            return $client->search($params2);

        } catch (\Exception $e) {
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
    private function getTimePeriod($alert)
    {
        echo "<br>From: " . date("Y-m-d H:i:s", strtotime('-' . $alert->minutes_back . ' minutes'));
        echo "<br>To: " . date("Y-m-d H:i:s");
        $start_date = "" . (date('U', strtotime('-' . $alert->minutes_back . ' minutes')) * 1000);
        $end_date = "" . (date('U') * 1000);
        $alert->criteria_temp = str_replace("%start_date%", $start_date, $alert->criteria);
        $alert->criteria_temp = str_replace("%end_date%", $end_date, $alert->criteria_temp);
        $alert->criteria_total_temp = str_replace("%start_date%", $start_date, $alert->criteria_total);
        $alert->criteria_total_temp = str_replace("%end_date%", $end_date, $alert->criteria_total_temp);
        return $alert;
    }

    /**
     * check all alerts
     */
    function getResult()
    {
        $l = new LibratoUtil();
        $start_time = date('U');
        echo "<pre>";
        //get data
        $alerts = Alert::all();
        foreach ($alerts as $alert) {
            echo "<h2>" . $alert->description . "</h2>";

            //add time constraint
            $alert = $this->getTimePeriod($alert);

            // echo $alert->criteria."<br>";
            $hits = $this->doSearch($alert, "alert");
            echo "<br>" . $hits . " hits found ";//.$alert->criteria;

            //search for total documents so that percentages can be calculated
            $hits_total = $this->doSearch($alert, "total");

            //check if we should be alerted
            $this->checkAlertCondition($alert, $hits, $hits_total);

            //update librato
            $this->updateLibrato($hits_total, $hits, $alert->librato_id);

            //screen output
            echo "<br>" . $hits_total . " total hits";
            if ($hits_total > 0) {
                echo "<br>" . number_format((($hits / $hits_total) * 100), 2) . "% hits";
                echo "<br>" . number_format((($hits / $hits_total) * 100), 2) . "% hits";
            } else {
                echo "<br>total hits are 0";
            }

            echo "<hr>";
            $this->alert_total_checks = $this->alert_total_checks + 1;
        }
        //update librato status
        $l->pushAppStatus($this->alert_total_checks, $start_time, date('U'));
    }

    /**
     * updates librato dashboard
     * @param $metric
     * @param $value
     */
    function updateLibrato($metric_ok, $metric_alert, $librato_id)
    {
        $librato = new LibratoUtil;
        //removing alert values as we are assuming that metric contains all responses

        $librato->push(($metric_ok - $metric_alert), $metric_alert, $librato_id);
    }


    /**
     * check if the alert conditions have been met and record the hits
     * @param $alert , the alert object
     * @param $hits , the absolute number of search hits
     * @param $total_hits , the total number of hits
     */
    function checkAlertCondition($alert, $hits, $total_hits)
    {
        $librato = new LibratoUtil;

        //absolute hit number check (greater than zero check)
        $result['check_gt0'] = $this->checkGt0($alert, $hits);

        //percentage hit check (greater than zero check)
        $result['check_gt0_pct'] = $this->checkGt0Pct($alert, $hits, $total_hits);

        //alert hits equal zero
        $result['check_e0'] = $this->checkE0($alert, $hits);

        // Display the results
        foreach ($result as $key => $value) {
            echo "<br>$key Consecutive failures = " . $value['consecutive_failures'];
        }
        $alert->save();
    }

    /**
     *
     * checks for equal zero alert
     *
     * @param $alert
     * @param $hits
     * @return mixed
     */
    private function checkE0($alert, $hits)
    {
        $librato = new LibratoUtil;
        if ($alert->alert_type == 'e0') {
            if ($hits == 0) {
                $alert->consecutive_failures_count_e0 = $alert->consecutive_failures_count_e0 + 1;
                if ($alert->consecutive_failures_count_e0 >= $alert->consecutive_failures_e0) { //check if the consecutive failure threshold has been met
                    $this->alert_run->total_alerts_equal_zero = $this->alert_run->total_alerts_equal_zero + 1;
                    $alert->zero_hit_alert_state = true;
                    if ($alert->alert_enabled_e0 == true) {//only send  notification if enabled
                        $this->sendMail($alert, $alert->description . " has $hits hits");
                        $librato->pushAnnotation($alert->librato_id, "zero-hits-alert-" . $alert->description, "The number of hits for the search you are monitoring is zero", "http://mytestlink.local", time(), time(), $alert->description);
                    }
                    echo "<br>zero hits alert threshold met";
                } else {
                    echo "<br>consecutive threshold count not me for equal zero";
                }
            } else {
                $alert->zero_hit_alert_state = false;
                $alert->consecutive_failures_count_e0 = 0;
            }
        }
        $result['consecutive_failures'] = $alert->consecutive_failures_count_e0;
        $alert->save();
        return $result;
    }

    /**
     *
     * checks for greater than zero percentage alert
     *
     * @param $alert
     * @param $hits
     * @param $total_hits
     */
    private function checkGt0Pct($alert, $hits, $total_hits)
    {
        $librato = new LibratoUtil;
        if ($alert->alert_type == 'gt0') {
            if ($total_hits > 0) {
                $alert_pct = (($hits / $total_hits) * 100);
            } else {
                $alert_pct = 0;
            }
            if ($alert->pct_of_total_threshold > 0 && $alert->pct_of_total_threshold < $alert_pct) {
                $alert->consecutive_failures_count_pct = $alert->consecutive_failures_count_pct + 1;
                if ($alert->consecutive_failures_count_pct >= $alert->consecutive_failures_pct) { //check if the consecutive failure threshold has been met
                    $this->alert_run->total_alerts_pct = $this->alert_run->total_alerts_pct + 1;
                    $alert->pct_alert_state = true;
                    if ($alert->alert_enabled_gt0_pct == true) {//only send  notification if enabled
                        $this->sendMail($alert, $alert->description . " exceeded " . $alert->pct_of_total_threshold . "%.");
                        //add librato annotation
                        $librato->pushAnnotation($alert->librato_id, "percentage-hit-alert-" . $alert->description, "The percentage for the search you are monitoring exceeded your threshold", "http://mytestlink.local", time(), time(), $alert->description);
                    }
                    echo "<br>gt0 percentage threshold met";
                } else {
                    echo "<br>consecutive threshold count not met for gt0 percetnage";
                }
            } else {
                $alert->pct_alert_state = false;
                $alert->consecutive_failures_count_pct = 0;
            }
        }
        $result['consecutive_failures'] = $alert->consecutive_failures_count_pct;
        $alert->save();
        return $result;
    }

    /**
     *
     * checks if the greater than zero check has been met
     *
     * @param $alert
     * @param $hits
     * @param $total_hits
     */
    private function checkGt0($alert, $hits)
    {
        // alert_enabled_gt0
        $librato = new LibratoUtil;
        if ($alert->alert_type == 'gt0') {
            if ($alert->number_of_hits > 0 && $alert->number_of_hits < $hits) {
                $alert->consecutive_failures_count = $alert->consecutive_failures_count + 1;
                if ($alert->consecutive_failures_count >= $alert->consecutive_failures) { //check if the consecutive failure threshold has been met
                    $this->alert_run->total_alerts_absolute = $this->alert_run->total_alerts_absolute + 1;
                    $alert->number_hit_alert_state = true;
                    if ($alert->alert_enabled_gt0 == true) {//only send  notification if enabled
                        ////send email
                        $this->sendMail($alert, $alert->description . " exceeded " . $alert->number_of_hits . " hits.");
                        //add librato annotation
                        $librato->pushAnnotation($alert->librato_id, "absolute-hit-alert-" . $alert->description, "The number of hits for the search you are monitoring exceeded your threshold", "http://mytestlink.local", time(), time(), $alert->description);
                    }
                    echo "<br>gt0 hit threshold met";
                } else {
                    echo "<br>consecutive threshold count not met for gt0";
                }
            } else {
                $alert->consecutive_failures_count = 0;
                $alert->number_hit_alert_state = false;
            }
        }
        $result['consecutive_failures'] = $alert->consecutive_failures_count;
        $alert->save();
        return $result;
    }


    /**
     * sends an email notification for a particular alert
     * @param Alert $alert Alert model
     * @param string $alert_description Description for email body
     */
    function sendMail($alert, $alert_description)
    {
        (new AlertMailer())->sendAlertMail($alert, $alert_description);
    }


    /**
     * search for a particular alert condition
     * @param $alert , $alert eloquent object
     * @param $query_type , alert query, or total query
     * @return int total hits
     * @todo remove es_type from db as it doesn't get used anymore, the search json can apply a type filter
     */
    function doSearch($alert, $query_type)
    {
        echo "\n***** running $query_type *****";
        try {
            $return_val = 0;
            $this->current_alert = $alert;
            if ($query_type == "alert") {
                echo "\nprocessing alert";
                if ($alert->criteria_temp != "") {
                    $params = json_decode($alert->criteria_temp, true);
                    $result = $this->searchELK($this->eu->getDateValues($alert->es_index), $alert->es_type, array($alert->es_host), $params, array(), 'count');
                    $return_val = $result['hits']['total'];
                } else {
                    echo "\nNo valid search query found for monitor";
                }
            } elseif ($query_type == "total") {
                if ($alert->criteria_total_temp != "") {
                    $params = json_decode($alert->criteria_total_temp, true);
                    $result = $this->searchELK($this->eu->getDateValues($alert->es_index), $alert->es_type, array($alert->es_host), $params, array(), 'count');
                    $return_val = $result['hits']['total'];
                } else {
                    echo "\nNo valid search query found for totals";
                }
            }
            echo "<br>index: " . $this->eu->getDateValues($alert->es_index);
            echo "<br>index type: " . $alert->es_type;
            echo "<br>index host: " . $alert->es_host;
            return $return_val;
        } catch (\Exception $e) {
            echo "\nerror doSearch: " . $e->getMessage();
            echo "\nalert criteria: " . $alert->criteria_temp;
            return 0;
        }
    }

    /**
     * returns elastic search exception
     * @param $error
     * @return mixed
     */
    function getESException($error)
    {
        $result['result_hits'] = 0;
        $result['result_code'] = $error['status'];
        $result['result'] = "error";
        $result['result_body'] = $error;
        return $result;
    }


    /**
     * create the test index
     * @param $host , the ES host
     */
    function createTestIndex()
    {
        try {
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
        } catch (\Exception $e) {
            echo "<pre>";
            echo "<h1>createTestIndex error</h1>";
            print_r($e->getMessage());
            //if index exists lets add the mappings
            $this->addTestMappings();

        }
    }


    /**
     * creates the test mappings
     */
    function addTestMappings()
    {
        echo "<br>adding mappings";
        try {
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
        } catch (\Exception $e) {
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
    function populateELKtestData()
    {

        $posts = Post::all();
        foreach ($posts as $post) {
            $this->addELKTestDoc($post);
        }
    }

    /**
     * adds a single test doc to the test index
     * @param $post
     */
    function addELKTestDoc($post)
    {
        try {
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
                    'created_at' => "" . $post->created_at,
                    'updated_at' => "" . $post->updated_at,
                    //   '_timestamp' => strtotime("".$post->updated_at)
                ]
            ];

            $response = $client->index($params);
            print_r($response);
        } catch (\Exception $e) {
            echo "<pre>";
            print_r($post);
            echo $e->getMessage();
        }

    }

}

