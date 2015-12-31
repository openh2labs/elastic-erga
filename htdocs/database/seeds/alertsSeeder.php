<?php

/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 13/12/15
 * Time: 11:34
 */

use Illuminate\Database\Seeder;
use App\alerts;

/**
 * Class alertsSeeder
 * run php artisan migrate:refresh --seed
 * to refresh the data if you make changes
 */

class alertsSeeder extends seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        DB::table('alerts')->delete();
        for($i=0; $i<30; $i++)
        {
          /*
            $filter = array();
            $filter ['bool']['must'][]['term']['content'] = $faker->word();
            $filter ['bool']['must'][]['range']['updated_at'][]['lte'] = "now-%minutes%"; //*
            */

            App:alerts::create(array(
            //'criteria' => 'some_search_field <> "'.$faker->sentence(3).'"',
            'criteria' => '{"query":{"query_string":{"analyze_wildcard":true,"query":"content: '.$faker->word().'"}},"filter":{"bool":{"must":[{"range":{"updated_at":{"gte":%start_date%,"lte":%end_date%,"format":"epoch_millis"}}}],"must_not":[]}}}',
            //'criteria' => json_encode($filter),
            'es_host' => '192.168.10.10:9200',
            'es_index' => 'default_v5',
            'es_type' => 'posts_v5',
            'es_datetime_field' => 'updated_at',
            'minutes_back' => 141440

        ));
        }


    }

}