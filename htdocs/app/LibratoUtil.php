<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 22/01/16
 * Time: 19:07
 */

namespace App;

use GuzzleHttp\Client;
use App\Librato;


class LibratoUtil
{

    public function push($metric_ok, $metric_alert, $librato_id){
        try{
            $l = Librato::find($librato_id);
            if($l != null){
                $this->send($l->uri, $l->username, $l->api_key, $l->gauge_ok, $l->gauge_alert, $metric_ok, $metric_alert, $l->saurce);
            }
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            echo "\nlibrato row not found";
        }
    }

    /**
     *
     * sends Librato alerts
     *
     * @param $url
     * @param $username
     * @param $api_key
     * @param $gauge_ok
     * @param $gauge_alert
     * @param $ok_value
     * @param $alert_value
     * @param $source
     */
    private function send($url, $username, $api_key, $gauge_ok, $gauge_alert, $ok_value, $alert_value, $source){
        //echo "$url, $username, $api_key, $gauge_ok, $gauge_alert, $ok_value, $alert_value, $source";
        echo "\nsending data to librato";

        $curl = curl_init($url);
        $curl_post_data = array(
            "gauges" => array(
                array("name" => $gauge_ok, "value" => $ok_value),
                array("name" => $gauge_alert, "value" => $alert_value, 'source' => $source)
            )
        );

        $headers = array(
            'Content-Type: application/json'
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));

        curl_setopt($curl, CURLOPT_USERPWD, "$username:$api_key");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        ## Show the payload of the POST
        #print_r($curl_post_data);
        $result = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
       // echo "HTTP Status Code: " . $http_status;
      //  echo "\n response from librato: ".$result;
    }


    /**
     *
     * pushes an annotation to librato
     *
     * @param $librato_id
     * @param $title
     * @param $description
     * @param $link
     * @param $start_time
     * @param $end_time
     */
    public function pushAnnotation($librato_id, $title, $description, $link, $start_time, $end_time, $alertDescription){
        try{
            $l = Librato::find($librato_id);
            if($l != null){
                echo "\nsending librato annotation";
                //$l->uri, $l->username, $l->api_key, $l->gauge_ok, $l->gauge_alert, $metric_ok, $metric_alert, $l->saurce
                $this->sendAnnotation($l->username, $l->api_key, $l->saurce, $title, $description, $link, $start_time, $end_time, $alertDescription);
            }
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            echo "\nlibrato row not found";
        }
    }

    /**
     *
     * send an annotation to librato
     *
     * @param $username
     * @param $api_key
     * @param $source
     * @param $title , the type of alert plus alert description
     * @param $description
     * @param $link
     * @param $start_time
     * @param $end_time
     * @param $alertDescription, the actual alert description without anything added to it
     */
    private function sendAnnotation($username, $api_key, $source, $title, $description, $link, $start_time, $end_time, $alertDescription){
        //echo "$url, $username, $api_key, $gauge_ok, $gauge_alert, $ok_value, $alert_value, $source";
        echo "\nsending data to librato";

        $curl = curl_init("https://metrics-api.librato.com/v1/annotations/elastic-erga-".str_replace(" ", "-",$title));
        $curl_post_data = array("title" => $title, "description" => $description, "start_time" => $start_time, "end_time" => $end_time); //@todo add link support

        $headers = array(
            'Content-Type: application/json'
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));

        curl_setopt($curl, CURLOPT_USERPWD, "$username:$api_key");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        ## Show the payload of the POST
        #print_r($curl_post_data);
        $result = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
         echo "HTTP Status Code: " . $http_status;
          echo "\n response from librato: ".$result;
    }
}