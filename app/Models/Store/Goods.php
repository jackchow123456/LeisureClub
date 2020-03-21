<?php

namespace App\Models\Store;

use App\Repository\Admin\GoodsRepository;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $guarded = [];

    protected $appends = [
        'sku', 'me'
    ];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('store.database.table_prefix') . 'goods');
        parent::__construct($attributes);
    }

    /**
     * 处理sku（获取）
     *
     * @param $key
     * @return false|string
     */
    public function getSkuAttribute($key)
    {
        return (new GoodsRepository($this))->getGoodsSkuInfo();
    }

    /**
     * 处理sku（设置）
     *
     * @param $key
     */
    public function setSkuAttribute($key)
    {
        $sku = json_decode($key, true);
        (new GoodsRepository($this))->handleSku($sku, $this);
    }

    /**
     * 处理商品画册（获取）
     *
     * @param $key
     * @return array
     */
    public function getMeAttribute($key)
    {
        return $this->mediaCategory ? $this->mediaCategory->medias()->pluck('path')->toArray() : [];
    }

    /**
     * 处理商品画册（设置）
     *
     * @param $key
     */
    public function setMeAttribute($key)
    {
        $mediaCategory = MediaCategory::updateOrCreate([
            'use' => '商品',
            'use_id' => $this->id,
        ], [
            'name' => $this->name,
            'store_id' => getStoreId(),
            'type' => 'image',
        ]);

        Media::where('mc_id', $mediaCategory->id)->delete();
        foreach ($key as $path) {
            Media::create([
                'store_id' => getStoreId(),
                'mc_id' => $mediaCategory->getKey(),
                'type' => 'image',
                'path' => getSavePath($path),
            ]);
        }
    }

    /**
     * 处理商品图片（设置）
     * @param $key
     */
    public function setImageAttribute($key)
    {
        $this->attributes['image'] = getSavePath($key);
    }

    /**
     * 产品媒体列表
     */
    public function mediaCategory()
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
