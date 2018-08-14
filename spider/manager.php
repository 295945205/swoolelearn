<?php

/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/13
 * Time: 下午6:35
 */
namespace spider;
class manager
{

    public $pool;

    public function __construct($workerNum)
    {
        $this->pool = new Swoole\Process\Pool($workerNum);
    }

    public function run()
    {
        $this->pool->on("WorkerStart",function ($pool,$workerId){

        });
    }
}