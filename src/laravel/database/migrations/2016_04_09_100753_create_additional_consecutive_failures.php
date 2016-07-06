<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalConsecutiveFailures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //
        Schema::table('alerts', function ($table) {
            $table->integer('consecutive_failures_pct')->default(2); //no of consecutive failures before alerting, defaults to 2, for percentage based
            $table->integer('consecutive_failures_count_pct')->defaul(0); //current total of consecutive failures for percentage

            $table->integer('consecutive_failures_e0')->default(2); //no of consecutive failures before alerting, defaults to 2, for zero based based
            $table->integer('consecutive_failures_count_e0')->defaul(0); //current total of consecutive failures for zero based
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
            $table->dropColumn('consecutive_failures_pct');
            $table->dropColumn('consecutive_failures_count_pct');

            $table->dropColumn('consecutive_failures_e0');
            $table->dropColumn('consecutive_failures_count_e0');
        });
    }
}
