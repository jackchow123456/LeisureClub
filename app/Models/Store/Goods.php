<?php

namespace App\Models\Store;

use App\Repository\Admin\GoodsRepository;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $guarded = [];


    protected $appends = [
        'sku'
    ];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('store.database.table_prefix') . 'goods');
        parent::__construct($attributes);
    }

    public function getSkuAttribute($key)
    {
        return (new GoodsRepository($this))->getGoodsSkuInfo();
    }


    /**
     * 产品媒体列表
     */
    public function medias()
    {
        return $this->hasOne(MediaCategory::class, 'use_id', 'id')->where('use', '商品');
    }

    /**
     * 产品销售属性列表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attrs()
    {
        return $this->hasMany(GoodsAttrMap::class, 'goods_id')
            ->with([
                'values',
                'values.media',
                'values.value',
                'attr'
            ]);
    }

    /**
     * 产品 SKU
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany(GoodsSku::class, 'goods_id', 'id');
    }

    /**
     * 产品 库存列表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stock()
    {
        return $this->hasMany(GoodsSkuStock::class, 'goods_id');
    }
}
