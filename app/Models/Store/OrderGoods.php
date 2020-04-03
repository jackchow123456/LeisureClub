<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $guarded = ['rec_id'];
    protected $primaryKey = "rec_id";
    protected $table = 'store_order_goods';

    public function return_goods()
    {
        return $this->belongsTo(ReturnGoods::class, 'rec_id', 'rec_id');
    }
}
