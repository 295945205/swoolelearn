<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/13
 * Time: 下午6:42
 */
namespace application;

use GuzzleHttp\Client;
use models\Account;
use Symfony\Component\DomCrawler\Crawler;

class getContent implements application{

    public static function run()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $nick_name = $redis->rPop('nick_name');
        if(empty($nick_name)){
            echo "list is empty.\n";
            sleep(1);
            $redis->close();
            exit();
        }
        else{
            $account = new Account();
            $url= $nick_name."/followers";
            $client = new Client([
                'base_uri' => 'https://www.zhihu.com/people/',
                'timeout' => 2,
            ]);
            $response = $client->get($url);
            $code = $response->getStatusCode();
            if($code != '200'){
                echo "请求失败";
                $redis->lPush('nick_name',$nick_name);
                exit();
            }
            $crawler = new Crawler();
        }
    }
}