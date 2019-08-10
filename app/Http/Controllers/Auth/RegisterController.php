<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Sms\SmsRepository;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'mobile' => ['required', 'string', 'unique:users'],
        ], [
            'name.required' => '用户名不能为空',
            'name.unique' => '用户名已经存在',
            'mobile.unique' => '手机号码已经存在',
            'password.confirmed' => '两次输入密码不一致',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'email' => '',
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * 注册
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'step' => 'required|in:1,2',
            'mobile' => 'required',
            'scenes' => 'required|in:register',
        ]);

        $step = $request->input('step');
        $mobile = $request->input('mobile');
        $scenes = $request->input('scenes');

        if ($step == 1) {

            $this->validate($request, [
                'code' => 'required',
            ]);

            $code = $request->input('code');


            $result = SmsRepository::checkMobileSms($mobile, $code, $scenes);

            if ($result) {
                return $this->success(['message' => 'ok']);
            }

            return $this->failed('验证码填写错误');
        }

        if ($step == 2) {

            if (!SmsRepository::isChecked($mobile, $scenes)) {
                return $this->failed('你验证码认证还没通过呢');
            }

            $this->validator($request->all())->validate();

            event(new Registered($user = $this->create($request->all())));

            $token = $this->guard()->login($user);

            return $this->success([
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => auth()->user(),
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);
        }

    }
}
