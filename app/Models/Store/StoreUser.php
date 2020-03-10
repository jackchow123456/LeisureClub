<?php


namespace App\Models\Store;


use Encore\Admin\Auth\Database\Administrator;

class StoreUser extends Administrator
{
    public function store()
    {
        return $this->hasOne(Store::class, 'user_id');
    }
}