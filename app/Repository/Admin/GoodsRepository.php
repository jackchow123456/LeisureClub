<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2020-03-18
 * Time: 16:01
 */

namespace App\Repository\Admin;

use App\Models\Store\Goods;
use App\Models\Store\GoodsSku;
use App\Models\Store\Media;
use App\Models\Store\MediaCategory;
use App\Models\Store\GoodsAttr;
use App\Models\Store\GoodsAttrValue;

class GoodsRepository
{
    public $model;

    public function __construct(Goods $model)
    {
        $this->model = $model;
    }

    public function getGoodsSkuInfo()
    {
        $sku = GoodsSku::where('goods_id',$this->model->id)->get();
        if (!$sku) return [];

        $data = [];

        $attr = [];
        foreach ($sku as $sk => $item) {
            $attrs = explode(',', $item->key_name);
            foreach ($attrs as $ar) {
                $attrInfo = explode(':', $ar);
                $attrName = current($attrInfo);
                $attrValue = end($attrInfo);
                !isset($attr[$attrName]) && $attr[$attrName] = [];
                if(!in_array($attrValue,$attr[$attrName]))  $attr[$attrName][] = $attrValue;
            }

            $sku[$sk]['pic'] = $item->mediaCategory ? $item->mediaCategory->medias : '';
        }

        $data['attrs'] = $attr;
        $data['sku'] = $sku;

        return json_encode($data);
    }


    /**
     * 处理上传的sku信息
     *
     * @param $sku
     * @param $model
     */
    public function handleSku($sku, $model)
    {
        if (empty($sku)) return;


        $attrs = $sku['attrs'];
        $this->handleSkuAttr($attrs);
        GoodsSku::where('goods_id', $model->id)->delete();

        $ignoreKey = ['price', 'stock'];
        $skus = $sku['sku'];

        foreach ($skus as $sk => $item) {
            $key = $keyName = [];
            $media = false;
            foreach ($item as $k => $vitem) {
                if (in_array($k, $ignoreKey)) continue;

                if (is_array($vitem) && $k == 'pic') {
                    $media = $this->handleSkuMedia($vitem);
                    continue;
                }

                $value = GoodsAttrValue::with(['attr' => function ($j) use ($k) {
                    $j->where('name', $k);
                }])->where('name', $vitem)->first();

                $key[] = $value->getKey();
                $keyName[] = $k . ':' . $vitem;
            }

            $key_name = implode(',', $keyName);
            $media_id = $media ? $media->getKey() : 0;

            $goodsSku = GoodsSku::create([
                'goods_id' => $model->id,
                'key' => implode('_', $key),
                'key_name' => $key_name,
                'media_id' => $media_id,
                'price' => $item['price'],
                'stock' => $item['stock'],
            ]);


            if ($media_id > 0) MediaCategory::where('id', $media_id)->update(['use_id' => $goodsSku->getKey(), 'name' => $model->name . "【{$key_name}】图片"]);
        }

    }

    /**
     * 处理上传规格的属性和属性值
     *
     * @param $attrs
     */
    protected function handleSkuAttr($attrs)
    {
        if (!$attrs) return;

        foreach ($attrs as $attr => $attrValues) {
            $goods_attr = GoodsAttr::updateOrCreate([
                'store_id' => getStoreId(),
                'name' => $attr,
            ], [
                'sort' => 0
            ]);

            foreach ($attrValues as $attrValue) {
                GoodsAttrValue::updateOrCreate([
                    'goods_attr_id' => $goods_attr->getKey(),
                        'store_id' => getStoreId(),
                    'name' => $attrValue,
                ], [
                    'sort' => 0
                ]);
            }
        }
    }

    /**
     * 处理规格图片
     *
     * @param $skuImages
     * @return |null
     */
    protected function handleSkuMedia($skuImages)
    {
        if (!$skuImages) return null;

        $mediaCategory = MediaCategory::create([
            'name' => '',
            'store_id' => getStoreId(),
            'use' => '商品规格',
            'type' => 'image',
        ]);

        foreach ($skuImages as $item) {
            Media::create([
                'store_id' => getStoreId(),
                'mc_id' => $mediaCategory->getKey(),
                'type' => 'image',
                'path' => $item,
            ]);
        }

        return $mediaCategory;
    }
}