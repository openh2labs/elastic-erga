<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/10/15
 * Time: 21:44
 */

namespace App\Http\Controllers;

use Elasticquent\ElasticquentTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\alerts;



class PostController extends \Eloquent {

  //  public function home(){
      //  $alert = alerts::all();
      //  $data = array('name'=>'woohoo!!!', 'alerts'=>$alert);

     //   return view ("alerts_home", $data);
   // }

    use ElasticquentTrait;

    //
    public $fillable = ['title', 'content', 'tags'];

    protected $mappingProperties = array(
        'title' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
        'content' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
        'tags' => [
            'type' => 'string',
            "analyzer" => "stop"
        ],
    );
}

