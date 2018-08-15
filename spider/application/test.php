<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/15
 * Time: 下午5:39
 */

namespace application;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class test implements application
{
    public static function run()
    {
        $client = new Client([
            'base_uri' => 'https://www.zhihu.com/people/',
            'timeout' => 2,
        ]);
        $response = $client->get('hantianfeng/followers');
        $code = $response->getStatusCode();
        $crawler = new Crawler();
        $crawler->addContent((string)$response->getBody());

        $followingNum = $crawler->filter('.NumberBoard-itemValue')->eq(0)->attr('title')??0;
        $followerNum = $crawler->filter('.NumberBoard-itemValue')->eq(1)->attr('title')??0;

    }
}
