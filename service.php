<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/3
 * Time: 下午2:20
 */
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9502);

$serv->set([
    'open_cpu_affinity' => 1,
//    'daemonize'         => 1,
    'reactor_num'       => 4,
    'worker_num'        => 8,
    'task_worker_num'   => 10,
]);

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function(swoole_server $serv, $fd, $from_id, $data) {
    echo "接收数据" . $data . "\n";
    $data = trim($data);
    $task_id = $serv->task($data, 0);
    $serv->send($fd, "分发任务，任务id为$task_id\n");
});

$serv->on('Task', function (swoole_server $serv, $task_id, $from_id, $data) {
    echo "Tasker进程接收到数据";
    echo "#{$serv->worker_id}\tonTask: [PID={$serv->worker_pid}]: task_id=$task_id, data_len=".strlen($data).".".PHP_EOL;
    $serv->finish($data);
});

$serv->on('Finish', function (swoole_server $serv, $task_id, $data) {
    echo "Task#$task_id finished, data_len=".strlen($data).PHP_EOL;
});



//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();

function onReceive($server, $fd, $from_id, $data) {
    $server->tick(1000, function() use ($server, $fd) {
        echo "100ms \n";
    });

    $server->after(5000, function() use ($server, $fd) {
        $server->stop();
    });
}