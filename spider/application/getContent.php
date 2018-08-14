<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/13
 * Time: 下午6:42
 */
//namespace spider\application;

class getContent implements application{

    public static function run()
    {
        $redis = new \Redis();
        $redis->pconnect('127.0.0.1', 6379);
        $url = "http://www.swoole.com/";
        $urlInfo = parse_url($url);
        $cli = new \swoole_http_client($urlInfo['host'], 80);
        $cli->setHeaders([
            'Host' => $urlInfo['host'],
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->get($urlInfo['path'], function ($cli) use ($redis) {
            sleep(5);
            $redis->lPush('key1',microtime(true));
            $cli->close();
        });
    }
}