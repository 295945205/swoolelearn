<?php

/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/13
 * Time: 下午6:35
 */

class manager
{
    public $pool;

    public function __construct($workerNum = 5)
    {
        $this->pool = new \Swoole\Process\Pool($workerNum);
    }

    public function run($className)
    {
        $this->pool->on("WorkerStart",function ()use ($className){
            $className::run();
        });

        $this->pool->start();
    }

}