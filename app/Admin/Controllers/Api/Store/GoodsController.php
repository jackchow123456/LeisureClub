<?php

namespace App\Admin\Controllers\Api\Store;

use App\Admin\Service\GoodsAttrService;
use App\Http\Controllers\Controller;
use App\Admin\Resources\GoodsAttr as GoodsAttrResources;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function getGoodsAttr(Request $request, GoodsAttrService $service)
    {
        $name = $request->input('name', '');

        $list = $service->getListByName($name);

        return GoodsAttrResources::collection($list);
    }

    public function createGoodsAttr(Request $request, GoodsAttrService $service)
    {
        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => '请输入名称',
        ]);

        $name = $request->input('name');

        $result = $service->createAttr($name);

        if (!$result) {
            return $this->failed('创建规格失败.');
        }

        return $this->success($result);
    }
}