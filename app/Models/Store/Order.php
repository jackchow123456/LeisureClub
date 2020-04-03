<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['order_id'];
    protected $primaryKey = "order_id";
    protected $appends = ['order_status_desc'];
    protected $table = 'store_orders';
    //订单状态（1：未付款，2：已付款，3:已取消，4:已退货）
    protected $order_status_desc = ['1' => '未付款', '2' => '已付款', '3' => '已取消', '4' => '已退货'];

    const ORDER_STATUS = [
        'NO_PAY' => 1,
        'PAYED' => 2,
        'CANCEL' => 3,
        'RETURNED' => 4,
    ];

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'order_id');
    }

    /**
     * 获取订单状态描述
     */
    public function getOrderStatusDescAttribute()
    {
        return $this->order_status_desc[$this->attributes['order_status']];
    }
}
