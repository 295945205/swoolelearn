<?php

/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/14
 * Time: 上午10:34
 */
class autoloader
{
    public static function getLoader()
    {
        spl_autoload_register('autoloader::ClassLoader');
    }

    private static function classLoader($class)
    {
//        if(strstr('spider',$class)!==null){
//            $fileName=str_replace('\\',DIRECTORY_SEPARATOR,__DIR__.$class.'.php');
//        }
//        else{
            $fileName=APPLICATION_PATH.DIRECTORY_SEPARATOR.$class.'.php';
//        }
        if(file_exists($fileName)){
            require $fileName;
        }
        else{
            echo 'not find class '.$class.'\n';
        }
    }
}