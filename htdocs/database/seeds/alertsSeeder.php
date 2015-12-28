<?php

/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 13/12/15
 * Time: 11:34
 */

use Illuminate\Database\Seeder;
use App\alerts;

class alertsSeeder extends seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        DB::table('alerts')->delete();
        for($i=0; $i<30; $i++)
        {
            App:alerts::create(array(
            //'criteria' => 'some_search_field <> "'.$faker->sentence(3).'"',
            'criteria' => '{"bool":{"must":[{"term":{"content":"'.$faker->sentence(1).'"}}{"updated_at":"now-%minutes%"}]}}',
            'es_host' => '192.168.10.10:9200',
            'es_index' => 'default',
            'es_type' => 'posts',
            'es_datetime_field' => 'updated_at'
        ));
        }


    }

}