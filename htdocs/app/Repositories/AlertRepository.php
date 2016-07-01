<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 17/03/16
 * Time: 09:07.
 */

namespace App\Repositories;

use DB;

class AlertRepository
{
    /**
     * returns all indices in alerts.
     */
    public function getAllIndices()
    {
        $t = DB::table('alerts')->select('es_index', 'es_host')->distinct()->get();

        return $t;
    }
}
