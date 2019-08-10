<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-08-03
 * Time: 20:35
 */

namespace App\Facades\Auth;

use Illuminate\Support\Facades\Facade;

class AuthRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Auth\AuthRepository';
    }
}