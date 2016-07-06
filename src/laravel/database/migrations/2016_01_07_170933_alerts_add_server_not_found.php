<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertsAddServerNotFound extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alerts', function ($table) {
            $table->boolean('es_config_error_state'); //if the host, index are not found the alert is in error state
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alerts', function ($table) {
            $table->dropColumn('es_config_error_state');
        });
    }
}
