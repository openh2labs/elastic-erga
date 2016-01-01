<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description',255); //the name of the alert condition
            $table->text('criteria'); //the json encoded elastic search query
            $table->text('criteria_total'); //the josn encoded elastic search query to get the total documents
            $table->string('es_host',255); //elastic search host
            $table->string('es_index',50); //elastic search index
            $table->string('es_type',50); //elastic search type
            $table->string('es_datetime_field'); // the date time field to use
            $table->integer('minutes_back'); //how far back to check
            $table->float('pct_of_total_threshold'); //alerts will fire if the percentage of hits exceeds this value
            $table->integer('number_of_hits'); //alerts will fire if the absolute number of hits exceeds this value
          //  $table->timestamps('updated_at');
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
        Schema::drop('alerts');
    }
}
