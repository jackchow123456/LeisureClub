<?php

namespace App\Repository\Store;

use App\Models\Store\Cart;
use App\Models\Store\Goods;
use App\Models\Store\GoodsSku;

/**
 * 购物车管理
 */
class CartRepository
{
    public $user_id;
    public $checkedList;

    /**
     * 加入购物车
     *
     * @param $user_id
     * @param $goods_id
     * @param $spec_id
     * @return array
     */
    public function addToCart($user_id, $goods_id, $spec_id)
    {
        // 先检查用户购物车存不存在，存在数量加一，不存在才加入
        $exist = Cart::where([
            ['user_id', '=', $user_id],
            ['goods_id', '=', $goods_id],
            ['spec_id', '=', $spec_id],
        ])->first();

        if (!$exist) {
            // 加入购物车
            $goods_info = Goods::find($goods_id);
            if (!$goods_info) {
                return returnMessage('非法操作,商品不存在!');
            }

            if ($goods_info->checkHasSpecGoods() && $spec_id == 0) {
                return returnMessage('非法操作,规格参数必须传递!');
            }

            // 规格处理
            $spec_name = '';
            $face_img = $goods_info->image;
            $member_price = $goods_info['price'];
            if ($spec_id > 0) {
                if (!$spec_info = GoodsSku::find($spec_id)) {
                    return returnMessage('非法操作.商品规格不存在');
                }
                $spec_name = $spec_info['key_name'];
                $member_price = $spec_info['price'];
                $face_img = $spec_info['face_img'];
            }

            $data = [
                'user_id' => $user_id,
                'goods_id' => $goods_id,
                'goods_name' => $goods_info->name,
                'face_img' => $face_img,
                'spec_id' => $spec_id,
                'spec_name' => $spec_name,
                'goods_price' => $goods_info['line_price'],
                'member_price' => $member_price,
            ];
            Cart::create($data);
        } else {
            $exist->goods_num = $exist->goods_num + 1;
            $exist->save();
        }

        return returnMessage('操作成功.', true);
    }

    // 获取列表
    public function getList($page, $page_size)
    {
        $list = Cart::where('user_id', $this->user_id)->Paginate($page_size);
        $row = $this->calculation();

        return ['list' => $list, 'row' => $row];
    }

    // 计算价格
    public function calculation()
    {
        $total_price = 0;

        $this->checkedList = $this->checkedList ?: Cart::getListByUserId($this->user_id, true);

        foreach ($this->checkedList as $item) {
            $total_price += $item['goods_num'] * $item['member_price'];
        }

        return ['total_price' => round($total_price, 2)];
    }


    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setCheckedList($list)
    {
        $this->checkedList = $list;
    }

}