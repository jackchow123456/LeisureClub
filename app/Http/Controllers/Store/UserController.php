<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * 获取用户地址列表
     *
     * @param Request $request
     * @return mixed
     */
    public function getAddressList(Request $request)
    {
        $id = $request->input('id');

        return $id > 0 ? $this->success($request->user()->address()->where('id', $id)->first())
            : $this->success($request->user()->address);
    }

    /**
     * 添加用户收货地址
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addAddress(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2',
            'phone' => 'required|regex:/^1\d{10}/',
            'area' => 'required|array',
            'address' => 'required',
            'isDefault' => 'required',
        ]);

        $userId = $request->user()->getkey();
        $name = $request->input('name');
        $phone = $request->input('phone');
        $area = $request->input('area');
        $address = $request->input('address');
        $isDefault = $request->input('isDefault');

        if (DB::connection()->table('user_address')->where('user_id', $userId)->count() >= 10) {
            return $this->failed("不能添加那么多的地址信息.");
        }

        $isDefault && DB::connection()->table('user_address')->where('user_id', $userId)->update(['is_default' => '否']);
        $isDefault = $isDefault ? '是' : '否';
        $areaList = DB::connection()->table('area')->whereIn('area_id', $area)->pluck('name');

        UserAddress::create([
            'user_id' => $userId,
            'name' => $name,
            'phone' => $phone,
            'province' => $areaList[0],
            'city' => $areaList[1],
            'area' => $areaList[2] ?? '',
            'town' => $areaList[3] ?? '',
            'address' => $address,
            'is_default' => $isDefault,
        ]);

        return $this->success(['msg' => '操作成功']);
    }

    // 获取用户个人信息
    public function profile(Request $request)
    {
        $user = $request->user();
        return $this->success($user);
    }
}
