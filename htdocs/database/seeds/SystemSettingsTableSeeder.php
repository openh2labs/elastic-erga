<?php

use Illuminate\Database\Seeder;

class SystemSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('system_settings')->insert([
            'setting_key' => "librato_username",
            'setting_value' => "-1",
        ]);
        DB::table('system_settings')->insert([
            'setting_key' => "librato_api_key",
            'setting_value' => "-1",
        ]);
        DB::table('system_settings')->insert([
            'setting_key' => "librato_url",
            'setting_value' => "http://",
        ]);
        DB::table('system_settings')->insert([
            'setting_key' => "librato_status_enabled",
            'setting_value' => "0",
        ]);
    }
}
