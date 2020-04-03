<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class ReturnGoods extends Model
{
    protected $guarded = [];
    protected $primaryKey = "return_id";
    protected $table = 'store_return_goods';
}
