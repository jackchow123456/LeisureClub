<?php

namespace App\Repository\Store;

use App\Models\Store\Cart;
use App\Models\Store\Goods;
use App\Models\Store\Order;
use App\Models\Store\OrderGoods;
use App\Models\Store\ReturnGoods;
use App\Repository\Store\CartRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 订单管理
 */
class OrderRepository
{
    public $cartCheckedList;
    public $orderGoodsInfo;

    // 限制入口
    protected function decrQueue()
    {
        Cache::decrement('submit.order.number');
    }

    // 限制入口
    protected function incrQueue()
    {
        if (Cache::increment('submit.order.number') > 99) {
            throw new \Exception('服务器繁忙，请稍后再来');
        }
    }

    // 扣减库存
    protected function decrementStock()
    {
        foreach ($this->cartCheckedList as $k => $v) {
            Goods::where('id', $v['goods_id'])->decrement('stock_num', $v['goods_num']);
        }
    }

    // 检查库存
    protected function checkStock()
    {
        foreach ($this->cartCheckedList as $item) {
            $goods_info = Goods::find($item['goods_id']);
            if ($goods_info['stock_num'] < 0) {
                throw new \Exception("商品{$item['goods']['name']}库存不足.");
            }
        }
    }

    // 组合订单商品信息
    public function combineOrderGoodsInfo()
    {
        foreach ($this->cartCheckedList as $item) {
            $this->orderGoodsInfo[] = [
                'goods_id' => $item['goods_id'],
                'spec_id' => $item['spec_id'] ?: 0,
                'spec_name' => $item['spec_name'] ?: '',
                'buy_num' => $item['goods_num'],
                'goods_name' => $item['goods_name'],
                'goods_price' => $item['member_price'],
            ];
        }
    }

    // 生成订单号规则
    protected function generateOrderSn()
    {
        return date('YmdHis') . rand(10000, 99999);
    }

    // 根绝传递过来的goods_info生成订单商品需要的商品信息
    protected function getGoodsList($goods_info)
    {
        $goods_info = collect($goods_info);
        $goods_ids = $goods_info->keyBy('goods_id')->keys();
        $goods_list = Goods::selectRaw(DB::raw(Goods::getBasicShowField()))->whereIn('goods_id', $goods_ids)->get()->keyBy('goods_id');

        $list = [];

        foreach ($goods_info as $item) {
            $row = $goods_list[$item['goods_id']];

            $data = $row->toArray();
            $data['goods_num'] = $item['goods_num'];

            if ($item['spec_id']) {
                $goods_id = $row->goods_id;
                $spec_id = data_get($item, 'spec_id');
                if ($row->checkHasSpecGoods() && !$spec_id) {
                    throw new \Exception("商品{$goods_id}必须传递规格 。");
                }

                if ($spec_id) {
                    $spec_goods = SpecGoods::find($spec_id);
                    if (!$spec_goods) {
                        throw new \Exception("商品{$goods_id}规格传递错误 。");
                    }
                    $data['face_img'] = $spec_goods->face_img;
                    $data['member_price'] = $spec_goods->price > 0 ? $spec_goods->price * $row->getDiscount() : $row->member_price;
                    $data['spec_id'] = $spec_goods->getKey();
                    $data['spec_name'] = $spec_goods->key_name;
                }
            }

            $list[$item['goods_id'] . '_' . $item['spec_id']] = $data;
        }

        return $list;
    }


    // 提交订单
    public function submitOrder($user_id, $entity_id, $goods_info,$addressId)
    {
        $this->cartCheckedList = $goods_info ? $this->getGoodsList($goods_info) : Cart::getListByUserId($user_id, true);;
        $addressId = $addressId ?: DB::connection()->table('user_address')->where('user_id',$user_id)->orderBy('is_default')->first()->id;

        if (empty($this->cartCheckedList)) {
            throw new \Exception('你没有提交任何商品');
        }

        $this->incrQueue();
        DB::beginTransaction();
        try {
            $this->decrementStock(); // 扣减库存
            $this->checkStock(); // 检查库存
            $this->combineOrderGoodsInfo();

            // 生成订单信息
            $cartService = new CartRepository();
            $cartService->setCheckedList($this->cartCheckedList);
            $row = $cartService->calculation();
            $order_info = [
                'order_sn' => $this->generateOrderSn(),
                'user_id' => $user_id,
                'goods_amount' => $row['total_price'],
                'total_amount' => $row['total_price'],
                'order_amount' => $row['total_price'],
                'address_id' => $addressId,
            ];

            $order = Order::create($order_info);

            //生成订单商品信息
            foreach ($this->orderGoodsInfo as $k => $v) {
                $v['order_id'] = $order->getKey();
                OrderGoods::create($v);
            }

            // 去除购物车 && 添加商品销售量
            foreach ($this->cartCheckedList as $k => $v) {
                Cart::where('id', $v['id'])->delete();
                Goods::where('id', $v['goods_id'])->increment('sell_count', $v['goods_num']);
            }

            DB::commit();
            $this->decrQueue();
            return ['msg' => '恭喜你，下单成功.', 'order_sn' => $order->order_sn, 'order_id' => $order->getKey()];

        } catch (\Exception $e) {
            DB::rollBack();
            $this->decrQueue();
            echo $e->getMessage();
            return false;
        }

    }

    // 获取订单列表
    public function getList($user_id, $order_status, $start_date, $end_date, $page_index, $page_size)
    {
        $condition = [];
        $condition[] = ['user_id', '=', $user_id];
        if ($order_status > 0) {
            $condition[] = ['order_status', '=', $order_status];
        }
        if ($start_date) {
            $condition[] = ['created_at', '>=', $start_date];
        }
        if ($end_date) {
            $condition[] = ['created_at', '<=', $end_date];
        }

        return Order::where($condition)->orderBy('order_id','desc')->Paginate($page_size);
    }

    // 获取订单详情
    public function getDetailById($id)
    {
        return Order::with('orderGoods', 'orderGoods.return_goods')->find($id);
    }

    // 申请退货商品
    public function returnGoods($return_info, $type, $reason)
    {
        DB::beginTransaction();
        try {
            foreach ($return_info as $item) {

                $exist = ReturnGoods::where('rec_id', $item['rec_id'])->first();

                if ($exist) {
                    throw new \Exception('申请商品中已经申请过退货，不能重复申请.');
                }

                $order_goods = OrderGoods::find($item['rec_id']);
                if (!$order_goods) {
                    throw new \Exception('申请商品中找不到订单商品信息.');
                }
                if ($item['return_num'] > $order_goods->buy_num) {
                    throw new \Exception('申请商品中退货数量超出上限.');
                }
                $order = Order::find($order_goods->order_id);
                if ($order->order_status != Order::ORDER_STATUS['PAYED']) {
                    throw new \Exception('订单状态不允许申请退货.');
                }

                ReturnGoods::create([
                    'rec_id' => $item['rec_id'],
                    'order_id' => $order->getKey(),
                    'goods_id' => $order_goods->goods_id,
                    'spec_id' => $order_goods->spec_id,
                    'type' => $type,
                    'return_num' => $item['return_num'],
                    'reason' => $reason,
                ]);

            }
            DB::commit();
            return returnMessage('申请退货成功.', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage('申请退货失败.原因：' . $e->getMessage());
        }


    }

}