<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Auth\AuthRepository;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use SmsManager;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 重新定义登录方式
     *
     * @return string
     */
    public function username()
    {
        return 'name';
    }

    /**
     * 重写登录方法（jwt）
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $credentials = $this->credentials($request);

        if (!$token = auth()->attempt($credentials)) {
            return $this->failed('登录失败,账号或者密码错误', '401');
        }

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * 发送验证码
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendSms(Request $request)
    {
        $this->validate($request, [
            'scenes' => 'required|in:register'
        ]);
        $mobile = $request->input('mobile');
        $scenes = $request->input('scenes');

        if (!AuthRepository::checkMobile($mobile)) {
            return $this->failed('该手机号码已注册.');
        }

        $result = SmsManager::validateSendable();
        if (!$result['success']) {
            return $this->failed($result['message']);
        }

        //验证数据
        $result = SmsManager::validateFields();
        if (!$result['success']) {
            return $this->failed($result['message']);
        }

        if (!config('app.debug')) {
            $result = SmsManager::requestVerifySms();
        } else {
            $result = SmsManager::generateResult(true, 'mobile', '发送成功');
            $faker['sent'] = true;
            $faker['to'] = $mobile;
            $faker['code'] = 8888;
            $faker['deadline'] = time() + 300;
            SmsManager::updateState($faker);
//            SmsManager::setCanResendAfter(60);
        }

        $store_data = json_encode(SmsManager::retrieveState());

        Redis::connection('default')->setex(
            "validate_code:$scenes:$mobile",
            300,
            $store_data
        );

        return $this->success($result);
    }

    /**
     * 测试验证
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkMobileSms(Request $request)
    {
        $this->validate($request, [
            'scenes' => 'required|in:register',
            'mobile' => 'required',
            'code' => 'required',
        ]);

        $mobile = $request->input('mobile');
        $scenes = $request->input('scenes');
        $code = $request->input('code');

        $result = checkMobileSms($mobile, $code, $scenes);

        if ($result) {
            return $this->success([]);
        }

        return $this->failed('error');
    }


}
