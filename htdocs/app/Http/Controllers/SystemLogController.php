<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/01/16
 * Time: 22:04
 */

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;

class SystemLogController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function home()
    {
        //$alert = alerts::all();
        $data = array('name'=>'woohoo!!!', 'alerts'=>'');

        return view ("system_log", $data);
       // echo "system log";
        //return view('user.profile', ['user' => User::findOrFail($id)]);
    }
}