<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/3
 * Time: 下午5:33
 */
$http = new swoole_http_server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();