<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsecutiveFailuresColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('alerts', function ($table) {
            $table->integer('consecutive_failures')->default(2); //no of consecutive failures before alerting, defaults to 2
            $table->integer('consecutive_failures_count')->defaul(0); //current total of consecutive failures
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('alerts', function ($table) {
            $table->dropColumn('consecutive_failures');
            $table->dropColumn('consecutive_failures_count');
        });
    }
}
