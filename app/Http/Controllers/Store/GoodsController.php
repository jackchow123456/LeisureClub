<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\AdImage;
use App\Models\Store\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{

    public function getGoodsList(Request $request)
    {
        $pageSize = $request->input('page_size', 15);
        $result = DB::connection()->table('store_goods')->selectRaw('id,name,image,price,line_price')->paginate($pageSize)->items();
        array_walk($result, function ($item) {
            $item->image_uri = $item->image ? url($item->image) : '';
        });

        return $this->success($result);
    }


    public function getGoodsDetail(Request $request, $goodsId)
    {
        $detail = Goods::query()->find($goodsId)->toArray();

        if (!empty($detail['me'])) {
            foreach ($detail['me'] as $k => $v) {
                $detail['me'][$k] = $v ? url($v) : '';
            }
        }

        return $this->success($detail);
    }
}
