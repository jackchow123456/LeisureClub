<?php


namespace App\Models\Store;


use Illuminate\Database\Eloquent\Model;

class GoodsSku extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('store.database.table_prefix') . 'goods_sku');
        parent::__construct($attributes);
    }


    /**
     * SKU 库存
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock()
    {
        return $this->hasOne(GoodsSkuStock::class, 'sku_id');
    }


    public function mediaCategory()
    {
        return $this->hasOne(MediaCategory::class, 'use_id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'id');
    }


    const status = [-1, 0, 1];
}