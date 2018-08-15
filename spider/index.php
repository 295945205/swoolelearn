<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/14
 * Time: 上午10:40
 */
use Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor/autoload.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'autoloader.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'manager.php';

define('ROOT_PATH',dirname(__FILE__));
define('APPLICATION_PATH',ROOT_PATH.DIRECTORY_SEPARATOR.'application');
autoloader::getLoader();

//初始化数据库
$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'spider',
    'username'  => 'root',
    'password'  => '13691132514',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$longopt = array(
    'type:',
    'worker_num::'
);

//获取参数并分发路由
$param = getopt('',$longopt);
$workerNum = isset($param['worker_num'])?$param['worker_num']:4;
if($workerNum && is_integer($workerNum) && 0<$workerNum && $workerNum<5000){
    $manager = new manager($workerNum);
}
else{
    $manager = new manager();
}
$className = $param['type'];
if(file_exists(APPLICATION_PATH.DIRECTORY_SEPARATOR.$className.'.php'))
{
    $manager->run($className);
}
else{
    echo "Error! Not found class:".$className;
}