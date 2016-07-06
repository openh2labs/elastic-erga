<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCyfeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('librato', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('uri');
            $table->string('username');
            $table->string('api_key');
            $table->string('gauge_ok'); //value of ok if set
            $table->string('gauge_alert'); //value of alert
            $table->string('source'); //source if set
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
        //
        Schema::drop('librato');
    }
}
