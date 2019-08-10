<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-08-03
 * Time: 20:30
 */

namespace App\Repository\Auth;

use App\User;

class AuthRepository
{

    /**
     * 确保手机号是唯一的
     *
     * @param $mobile
     * @return bool
     */
    public function checkMobile($mobile)
    {
        return User::where('mobile', $mobile)->first() ? false : true;
    }

    /**
     * 确保用户名是唯一的
     *
     * @param $name
     * @return bool
     */
    public function checkUserName($name)
    {
        return User::where('name', $name)->first() ? false : true;
    }
}