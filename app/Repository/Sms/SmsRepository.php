<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-08-03
 * Time: 20:30
 */

namespace App\Repository\Sms;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class SmsRepository
{
    protected $activeKey = 'state_'; // 认证状态Key
    protected $activeTime = 120; // 认证状态失效时间

    /**
     * 验证验证码
     *
     * @param $mobile
     * @param $code
     * @param $scenes
     * @return bool
     */
    public function checkMobileSms($mobile, $code, $scenes)
    {
        $data = json_decode(Redis::connection('default')->get("validate_code:$scenes:$mobile"), true);

        if ($data && $data['code'] == $code) {
            Cache::put($this->activeKey . $scenes . '_' . $mobile, true, $this->activeTime);
            Redis::connection('default')->del("validate_code:$scenes:$mobile");
            return true;
        }

        return false;
    }

    /**
     * 判断是否已经认证过
     *
     * @param $mobile
     * @param $scenes
     * @return bool
     */
    public function isChecked($mobile, $scenes)
    {
        return Cache::get($this->activeKey . $scenes . '_' . $mobile, true, $this->activeTime) ?: false;
    }

}