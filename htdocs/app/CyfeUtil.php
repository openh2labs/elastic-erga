<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 22/01/16
 * Time: 08:49.
 */

namespace App;

use GuzzleHttp\Client;

class CyfeUtil
{
    public function sendData()
    {

       // $request = \Illuminate\Http\Request::create('https://app.cyfe.com/api/push/56a1dddfe89813214667461868558', 'POST', ['data' => array("Date"=>'20160122', "users"=>10), 'param2' => 'value2']);

        $data = array();
        $data['data'][] = array('Date' => '20160122', 'Users' => '10');
        $data['onduplicate'] = array('Users' => 'replace');
        $data['color'] = array('Users' => '#52ff7f');
        $data['type'] = array('Users' => 'line');

        $client = new Client(['debug' => true, 'exceptions' => false, 'headers' => ['Authorization' => 'Bearer '.'', 'Accept' => 'application/json'], 'verify' => false]);
        echo "\n just about to send";
        $request = $client->post('https://app.cyfe.com/api/push/56a1dddfe89813214667461868558', array(), array(
            'data' => array('Users' => 'replace'),
            'file_field' => '@/path/to/file.xml',
        ));
        echo "\n about to send";
        $response = $request->send();
        echo '@@@';

        die;

        /*
        $request = $client->request('POST', 'https://app.cyfe.com/api/push/56a1dddfe89813214667461868558', [
            'json' => [$data]
        ]);
        */

        $request = $client->post('https://app.cyfe.com/api/push/56a1dddfe89813214667461868558', array(
            'content-type' => 'application/json',
        ));
       // $request->addPostFields($data);

        //$request->setBody(); #set body!
        $request->send();

        echo $request->getStatusCode();

        echo $request->getHeader('content-type');
        // 'application/json; charset=utf8'
        echo $request->getBody();
        // {"type":"User"...'

        echo '<br>cyfe message sent';
    }

    public function sendCurlData($uri, $metric, $value)
    {
        $endpoint = 'https://app.cyfe.com/api/push/56a1dddfe89813214667461868558';
        $data = array();
        $data['data'][] = array('Date' => '20160122', 'Users' => rand(0, 205));
        $data['onduplicate'] = array('Users' => 'replace');
        $data['color'] = array('Users' => '#52ff7f');
        $data['type'] = array('Users' => 'line');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (stripos($status, '200') !== false) {
            echo 'success';
        } else {
            echo 'failure';
        }
    }
}
