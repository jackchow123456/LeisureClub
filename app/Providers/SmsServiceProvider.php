<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Toplan\PhpSms\Agent;
use Toplan\PhpSms\Sms;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Sms::scheme([
            'Feige' => [
                '20 backup',
                'sendTemplateSms' => function ($agent, $to, $content, $data) {

                    // 获取配置(如果设置了的话):
                    $url = 'http://api.feige.ee/SmsService/Template';
                    array_pop($data);
                    $content = $Content = implode('||', $data);
                    $params = [
                        'Content' => $content,
                        'Mobile' => $to,
                    ];

                    // 可使用的内置方法:
                    $result = $agent->curlPost($url, array_merge($agent->config(), $params)); //post

                    if ($result['request']) {
                        $agent->result(Agent::INFO, $result['response']);
                        $result = json_decode($result['response'], true);
                        $agent->result(Agent::CODE, $result['Code']);
                        if ($result['Message'] === 'OK') {
                            $agent->result(Agent::SUCCESS, true);
                        }
                    } else {
                        $agent->result(Agent::INFO, 'request failed');
                    }

                },
            ]
        ]);
    }
}
