<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 20/09/15
 * Time: 17:32.
 */

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param int $id
     *
     * @return Response
     */
    public function showProfile($id)
    {
        return view('user.profile', ['user' => User::findOrFail($id)]);
    }
}
