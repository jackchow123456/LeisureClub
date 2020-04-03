<?php

namespace App\Http\Controllers\Store;

use App\Repository\Store\CartRepository as Service;
use App\Facades\Store\CartRepository;
use App\Models\Store\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * 加入购物车
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'goods_id' => 'required|integer|gt:0',
            'spec_goods_id' => 'nullable|integer|gt:0'
        ]);

        $user_id = $request->user()->getKey();
        $goods_id = $request->input('goods_id');
        $spec_goods_id = $request->input('spec_goods_id', 0);

        $result = CartRepository::addToCart($user_id, $goods_id, $spec_goods_id);

        if (!$result['success']) {
            return $this->failed($result['msg']);
        }

        return $this->success(['msg' => '加入购物车成功.']);
    }

    /**
     * 移出购物车
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array'
        ]);
        $ids = $request->input('ids', []);

        if (Cart::whereIn('id', $ids)->delete()) {
            return $this->success(['msg' => '操作成功.']);
        }
        return $this->failed('操作失败.');
    }

    /**
     * 添加/减少商品
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'type' => 'required|in:1,2'
        ]);
        $type = $request->input('type');
        if ($type == 1) {
            $result = Cart::where("id", $id)->increment('goods_num');
        } else {
            $result = Cart::where("id", $id)->decrement('goods_num');
        }
        if ($result) {
            return $this->success(['msg' => '操作成功.']);
        }
        return $this->failed('操作失败.');
    }

    // 购物车选择商品
    public function checked(Request $request, Service $service)
    {
        $this->validate($request, [
            'ids' => 'array'
        ]);
        $ids = $request->input('ids', []);
        $user_id = $request->user()->getKey();

        Cart::where('user_id', '=', $user_id)->update(['is_checked' => 0]);
        Cart::whereIn('id', $ids)->update(['is_checked' => 1]);
        $service->setUserId($user_id);

        return $this->success($service->calculation());
    }

    // 购物车主页
    public function index(Request $request, Service $service)
    {
        $this->validate($request, [
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
        ]);

        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 15);

        $user_id = $request->user()->getKey();

        $service->setUserId($user_id);
        $data = $service->getList($page, $page_size);

        return $this->success($data);
    }

    // 获取购物晨勾选商品列表
    public function getCartChecked(Request $request)
    {
        $user_id = $request->user()->getKey();
        $result = Cart::getListByUserId($user_id, true);

        return $this->success($result);
    }

}
