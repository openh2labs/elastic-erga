<?php

namespace App;

use Elasticquent\ElasticquentTrait;
//use illuminate\html;
//use Illuminate\Database\Eloquent\Model;

class Post extends \Eloquent
{
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
