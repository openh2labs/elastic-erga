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
            $filter = array();
            $filter ['bool']['must'][]['term']['content'] = $faker->sentence(1);
         //   $filter ['range']['update_at']['gt'] = "now-%minutes%";
            $filter ['bool']['must'][]['range']['update_at']['gte'] = "now-%minutes%";
          //  $filter ['bool']['must'][]['range']['update_at']['lte'] = "now";

            App:alerts::create(array(
            //'criteria' => 'some_search_field <> "'.$faker->sentence(3).'"',

            //'criteria' => '{"bool":{"must":[{"term":{"content":"'.$faker->sentence(1).'"}}{"updated_at":"now-%minutes%"}]}}',
            'criteria' => json_encode($filter),
            'es_host' => '192.168.10.10:9200',
            'es_index' => 'default_v2',
            'es_type' => 'posts_v2',
            'es_datetime_field' => 'updated_at',
            'minutes_back' => 1440

        ));
        }


    }

}