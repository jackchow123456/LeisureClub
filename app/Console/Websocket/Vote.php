<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-08-01
 * Time: 21:38
 */

namespace App\Console\Websocket;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class Vote extends Command
{
    protected $signature = 'redis:subscribe';

    protected $description = 'Subscribe to a Redis channel for websocket.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line("订阅开启中...");
        $uri = "ws://127.0.0.1:1216";
        $websocket = new \WebSocket\Client($uri);
        try {
            Redis::connection('default')->subscribe(['example'], function ($data) use ($websocket, $uri) {
                $this->info("收到的信息：{$data}");
                if (!$websocket->isConnected()) {
                    $websocket = new \WebSocket\Client($uri);
                }
                $websocket->send($data);
                $websocket->close();
            });
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }

}