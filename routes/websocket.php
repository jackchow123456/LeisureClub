<?php

use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/
Websocket::on('connect', function ($websocket, Request $request) {
    // called while socket on connect
    echo 'connect' . PHP_EOL;
    $websocket->emit('message', 'welcome');
});

Websocket::on('disconnect', function ($websocket) {
    // called while socket on disconnect
});

Websocket::on('example', function ($websocket, $data) {
//    $websocket->emit('message', 'example');
    echo $data . PHP_EOL;
    $websocket->broadcast()->emit('example', $data);
});

Websocket::on('vote', function ($websocket, $data) {
    // called while socket on connect
    $data = str_replace("\"", "\\\"", $data);
    $websocket->broadcast()->emit('vote', $data);
});
