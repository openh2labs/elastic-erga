<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertExecutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_executions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description',255);
            $table->integer('duration');
            $table->integer('total_alerts_absolute'); //number of alerts that got activated based on absolute number of hits
            $table->integer('total_alerts_pct'); //number of alerts that got activated based on percentages of hits
            $table->integer('total_alerts_equal_zero'); //number of alerts that got activated based on zero hits
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('alert_executions');
    }
}
