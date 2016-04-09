<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalEnabledAlertStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alerts', function ($table) {
            $table->boolean('alert_enabled_gt0')->default(true); //whether or not to fire of an alert notification for greater than zero alerts
            $table->boolean('alert_enabled_gt0_pct')->defaul(true); //whether or not to fire of an alert notification for greater than zero percentage alerts
            $table->boolean('alert_enabled_e0')->defaul(true); //whether or not to fire of an alert notification for equal zero alerts
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
            $table->dropColumn('alert_enabled_gt0');
            $table->dropColumn('alert_enabled_gt0_pct');
            $table->dropColumn('alert_enabled_e0');
        });
    }
}
