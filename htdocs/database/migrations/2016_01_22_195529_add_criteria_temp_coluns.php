<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCriteriaTempColuns extends Migration
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
            $table->text('criteria_temp');
            $table->text('criteria_total_temp');
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
        //
        Schema::table('alerts', function ($table) {
            $table->dropColumn('criteria_temp');
            $table->dropColumn('criteria_total_temp');
        });
    }
}
