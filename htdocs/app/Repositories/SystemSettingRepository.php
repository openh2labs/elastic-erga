<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 15/02/16
 * Time: 07:54.
 */

namespace App\Repositories;

//use App\User;
use App\SystemSetting;

class SystemSettingRepository
{
    /**
     * return the setting value for a given key.
     *
     * @param $key
     *
     * @return mixed
     */
    public function forKey($key)
    {
        $s = SystemSetting::where('setting_key', $key)->get();
        if (count($s) == 1) {
            return $s[0]->setting_value;
        } else {
            return -1;
        }
    }

    /**
     * Get all of the tasks for a given user.
     *
     * @param User $user
     *
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Task::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
