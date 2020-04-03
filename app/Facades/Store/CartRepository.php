<?php

namespace App\Facades\Store;

use Illuminate\Support\Facades\Facade;

class CartRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        //这里一定要return一个绑定的键名，代表你接下来会获得那个键名绑定的对象
        return "Store\CartRepository";
    }
}