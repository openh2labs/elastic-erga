<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);
        $this->call('alertsSeeder');
        $this->command->info('alerts table seeded!');

        $this->call('PostsTableSeeder');
        $this->command->info('posts table seeded!');

        Model::reguard();
    }
}
