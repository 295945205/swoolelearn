<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/13
 * Time: 下午2:00
 */

$redis = new Redis();
//$redis->connect('127.0.0.1',6379);
//$redis->lPush('key1',1,2,3,4,5,6,7,8,9,10,11,12,13);
//$redis->close();
//exit();
$workerNum = 10;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "key1";
    while (true) {
        $msgs = $redis->brpop($key, 2);
        if ( $msgs == null) continue;
        echo $msgs."\n";
    }
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();