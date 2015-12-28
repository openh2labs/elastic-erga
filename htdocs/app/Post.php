<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elasticsearch\ClientBuilder;
//use Elasticquent\ElasticquentTrait;
//use illuminate\html;
//use Illuminate\Database\Eloquent\Model;

//this is based on tutorial here: http://www.fullstackstanley.com/read/simple-search-with-laravel-and-elasticsearch

class Post extends Model
{
    /*
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
        'updated_at' => [
            'type' => 'date'
        ]
    );
    */


}


