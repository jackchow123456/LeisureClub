<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-11-27
 * Time: 11:05
 */

namespace App\Admin\Service;

use App\Models\Store\GoodsAttr;

class GoodsAttrService
{
    public function getListByName($name)
    {
        return GoodsAttr::with('values')->when($name, function ($j) use ($name) {
            $j->where('name', 'LIKE', "{$name}%");
        })->paginate();
    }

    public function createAttr($name, $storeId = 0, $sort = 0)
    {
        return GoodsAttr::create([
            'name' => $name,
            'store_id' => $storeId,
            'sort' => $sort
        ]);
    }
}