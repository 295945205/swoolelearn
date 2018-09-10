<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/21
 * Time: 上午10:24
 */
namespace application\zhenguo;

use application\application;
use GuzzleHttp\Client;
use models\House;
use Symfony\Component\DomCrawler\Crawler;

class login implements application
{

    public static $redis;
    public static $crawler;
    public static $client;

    public static function init()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        self::$redis = $redis;

        self::$client = new Client([
            'base_uri' => 'https://phoenix.meituan.com/housing/',
            'timeout' => 4,
            'headers'=>[
                'User-Agent'=>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
                'Host'=>'phoenix.meituan.com',
                'Upgrade-Insecure-Requests'=>1,
                'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
            ],
        ]);

        self::$crawler = new Crawler();
    }

    public static function run()
    {
        self::init();
        $roomId = self::$redis->incr("roomId");
        while ($roomId<1270000){
            $response = self::$client->get((string)$roomId);
            $code = $response->getStatusCode();
            if ($code != '200') {
                echo "roomId:".$roomId."请求失败";
                $roomId=self::$redis->incr("roomId");
                continue;
            }
            self::$crawler->addContent((string)$response->getBody());
            if(self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/h2')->count()!=0 && self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/h2')->text()??''=='该产品不存在'){
                echo "roomId:".$roomId."不存在\n";
                self::$crawler->clear();
                $roomId=self::$redis->incr("roomId");
                continue;
            }
            $houseName = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[1]/div[1]/h2')->text();
            $houseType = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[1]/div[2]/span[1]')->text();
            $houseNum = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[1]/div[2]/span[2]')->text();
            $houseArea = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[1]/div[2]/span[3]')->text();
            $housePerson = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[1]/div[2]/span[4]')->text();
            $houseLocation = self::$crawler->filter('.product-detail__location')->text();

            $fangdongName = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[2]/dl/dd[1]/span/a')->text();
            $fangdongImg = self::$crawler->filterXPath('//*[@id="J-layout"]/div[2]/div/div[2]/div/div[2]/section[2]/dl/dt/a/img')->attr('src');

            $house=new House();
            $house->house_name=$houseName;
            $house->house_type=$houseType;
            $house->house_num=$houseNum;
            $house->house_area=$houseArea;
            $house->house_person=$housePerson;
            $house->house_location=$houseLocation;
            $house->fangdong_name=$fangdongName;
            $house->fangdong_img=$fangdongImg;
            $house->save();
            self::$crawler->clear();
            $roomId = self::$redis->incr("roomId");
        }
    }

}