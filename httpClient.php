<?php
/**
 * Created by PhpStorm.
 * User: zehao
 * Date: 2018/8/12
 * Time: 下午5:23
 */
class Crawler
{
    private $url;
    private $redis;
    private $toVisit = [];
    private $loaded = false;
    public function __construct($url)
    {
        $this->url = $url;
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $this->redis=$redis;
    }

    public function visitOneDegree()
    {
        $this->visit($this->url, true);
        $retryCount = 100;
        do {
            sleep(1);
            $retryCount--;
        } while ($retryCount > 0 && $this->loaded == false);
        $this->visitAll();
    }

    private function loadPage($content)
    {
        print 'loadPage'."\n";
        $pattern = '#((http|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i';
        preg_match_all($pattern, $content, $matched);
        foreach ($matched[0] as $url) {
            if (in_array($url, $this->toVisit)) {
                continue;
            }
            $this->toVisit[] = $url;
        }
    }

    private function visitAll()
    {
        print 'visitall'."\n";
        foreach ($this->toVisit as $url) {
            $this->visit($url);
        }
    }

    public function visit($url, $root = false)
    {
        $urlInfo = parse_url($url);
        $cli = new swoole_http_client($urlInfo['host'], 80);
        $cli->setHeaders([
            'Host' => $urlInfo['host'],
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->get($urlInfo['path'], function ($cli) use ($root) {
            sleep(1);
            $this->redis->lPush('key1',microtime(true));
            if ($root) {
                $this->loadPage($cli->body);
                $this->loaded = true;
            }
            $cli->close();
        });
    }
}

$start = microtime(true);

$url = 'http://www.swoole.com/';
$ins = new Crawler($url);
//$ins->visitOneDegree();
$ins->visit($url);
$ins->visit($url);
$ins->visit($url);
$ins->visit($url);
$ins->visit($url);
$timeUsed = microtime(true) - $start;
echo "time used: " . $timeUsed;
exit();


