<?php

namespace App\Http\Controllers\Store;

use App\Facades\Store\OrderRepository;
use App\Models\Store\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    // 提交订单
    public function store(Request $request)
    {
        $this->validate($request, [
            'goods_info' => 'array',
            'goods_info.*.goods_id' => 'integer|gt:0',
            'goods_info.*.goods_num' => 'integer|gt:0',
            'goods_info.*.spec_id' => 'nullable|integer|gt:0',
        ]);
        $goods_info = $request->input('goods_info', []);
        $addressId = $request->input('address_id');
        $user_id = $request->user()->getKey();
        $entity_id = $request->user()->entity_id;

        if ($data = OrderRepository::submitOrder($user_id, $entity_id, $goods_info, $addressId)) {
            return $this->success($data);
        }
        return $this->failed('网络异常.');
    }

    // 订单列表
    public function index(Request $request)
    {
        $this->validate($request, [
            'type' => 'nullable|in:0,1,2,3,4',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
        ]);
        $order_status = $request->input('type', 0);
        $start_date = $request->input('start_date', 0);
        $end_date = $request->input('end_date', 0);
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 15);
        $user_id = $request->user()->getKey();

        $data = OrderRepository::getList($user_id, $order_status, $start_date, $end_date, $page, $page_size);

        return $this->success($data);
    }

    //订单详情
    public function show($id)
    {
        $data = OrderRepository::getDetailById($id);

        return $this->success($data);
    }

    // 取消订单
    public function cancel(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer|gt:0'
        ], [
            'order_id.required' => '缺少订单id参数'
        ]);

        $user_id = $request->user()->getKey();
        $order_id = $request->input('order_id');

        $order = Order::where([
            ['order_id', '=', $order_id],
            ['user_id', '=', $user_id],
        ])->first();

        if (!$order) {
            return $this->failed('该订单不是你的，不能操作');
        }

        if ($order->order_status != Order::ORDER_STATUS['NO_PAY']) {
            return $this->failed('该订单状态不能取消');
        }

        $order->order_status = Order::ORDER_STATUS['CANCEL'];
        $order->save();

        return $this->success(['msg' => '取消订单成功.']);
    }

    // 申请退货
    public function returnGoods(Request $request)
    {
        $this->validate($request, [
            'return_info' => 'required|array',
            'return_info.*.rec_id' => 'required|integer|gt:0',
            'return_info.*.return_num' => 'required|integer|gt:0',
            'type' => 'required|integer|in:1,2',
            'reason' => 'required|string'
        ]);

        $return_info = $request->input('return_info');
        $type = $request->input('type');
        $reason = $request->input('reason', '');

        $result = OrderRepository::returnGoods($return_info, $type, $reason);

        if (!$result['success']) {
            return $this->failed($result['msg']);
        }

        return $this->success($result);
    }

    // 支付订单
    public function pay(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required',
            'payment' => 'required',
        ]);

        $orderId = $request->input('order_id');
        $payment = $request->input('payment');

        Order::where('order_id', $orderId)->update([
            'order_status' => 2,
            'payment' => $payment,
        ]);

        return $this->success(['msg' => '支付成功']);
    }
}

