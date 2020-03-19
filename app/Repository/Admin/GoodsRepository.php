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
}