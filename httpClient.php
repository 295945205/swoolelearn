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
    private $toVisit = [];
    private $loaded = false;
    public function __construct($url)
    {
        $this->url = $url;
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

    private function visit($url, $root = false)
    {
        $urlInfo = parse_url($url);
        Swoole\Async::dnsLookup($urlInfo['host'], function ($domainName, $ip) use($urlInfo, $root) {
            $cli = new swoole_http_client($ip, 80);
            $cli->setHeaders([
                'Host' => $domainName,
                'Accept' => 'text/html,application/xhtml+xml,application/xml',
                'Accept-Encoding' => 'gzip',
            ]);
            $cli->get($urlInfo['path'], function ($cli) use ($root) {
                if ($root) {
                    $this->loadPage($cli->body);
                    $this->loaded = true;
                }
            });
        });
    }
}

$start = microtime(true);

$url = 'http://www.swoole.com/';
$ins = new Crawler($url);
$ins->visitOneDegree();

$timeUsed = microtime(true) - $start;
echo "time used: " . $timeUsed;



