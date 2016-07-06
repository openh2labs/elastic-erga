<?php

/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 13/12/15
 * Time: 11:34
 */

use Illuminate\Database\Seeder;
use App\Alert;

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

            App:Alert::create(array(
            'description' => $faker->sentence(3),
            'criteria' => '{"query":{"query_string":{"analyze_wildcard":true,"query":"content: '.$faker->word().'"}},"filter":{"bool":{"must":[{"range":{"updated_at":{"gte":%start_date%,"lte":%end_date%,"format":"epoch_millis"}}}],"must_not":[]}}}',
            'criteria_total' => '{"query":{"query_string":{"query":"*","analyze_wildcard":true}},"filter":{"bool":{"must":[{"range":{"updated_at":{"gte":%start_date%,"lte":%end_date%,"format":"epoch_millis"}}}],"must_not":[]}}}',
            'es_host' => '192.168.10.10:9200',
            'es_index' => 'default_v5',
            'es_type' => 'posts_v5',
            'es_datetime_field' => 'updated_at',
            'minutes_back' => 141440,
            'pct_of_total_threshold' => rand(0,5),
            'number_of_hits' => rand(0,20),
            'alert_email_recipient' => $faker->email(),
            'alert_email_sender' => $faker->email(),
            'alert_type' => 'gt0'
        ));
        }


    }

}