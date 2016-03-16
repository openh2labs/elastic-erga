<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 14/03/16
 * Time: 11:55
 */

namespace App\Repositories;
use DB;


class AlertExecutionsRepository
{

    /**
     *
     * purges old records over a certain number of days back
     *
     * @param $daysBack
     */
    function purge($daysBack){
        $dt =  date('Y-m-d H:i:s', strtotime('-'.$daysBack.' days'));
        DB::table('alert_executions')->where('created_at', '>=', $dt)->delete();
    }
}