<?php

$logFile = fopen("acess_log.txt", "r") or die("Unable to open file!");
$views = 0;
$urls = [];
$statusCodes = [];
$traffic = 0;
$crawlers = [
    'Google' => 0,
    'Bing' => 0,
    'Baidu' => 0,
    'Yandex' => 0,
];

/**
 * @param string $str
 * @param int[] $crawlers
 * @return array
 */
function findCrawlers(string $str, array $crawlers)
{
    $crawlerList = [
        'Googlebot',
        'bingbot',
        'Baiduspider',
        'YandexBot',
    ];
    foreach($crawlerList as $crawler) {
        if (stripos($str,$crawler)) {
            if ($crawler == "Googlebot") {
                $crawlers["Google"]++;
            } else if ($crawler == "bingbot") {
                $crawlers["Bing"]++;
            } else if ($crawler == "Baiduspider") {
                $crawlers["Baidu"]++;
            } else if ($crawler == "YandexBot") {
                $crawlers["Yandex"]++;
            }
        }
    }
    return $crawlers;
}

while(!feof($logFile)) {
    $line = fgets($logFile);

    preg_match('/^[\w\W]+\"\w{3,4}\s(\S+)[\w\W]+\"\s(\d+)\s(\d+)\s\"[\w\W]+\"\s\"(\S+\s\([\w\W]+\))(\s(\S+)\s[\w\W]+)?\"/', $line, $result);

    $views++;
    $urls[] = $result[1];
    $statusCodes[] = $result[2];
    if ($result[2] == "200")
        $traffic += $result[3];
    $crawlers = findCrawlers($result[4], $crawlers);
}

$parsedFiled = json_encode(["views" => $views, "urls" => count(array_unique($urls)), "traffic" => $traffic, "crawlers" => $crawlers, "statusCodes" => array_count_values($statusCodes)]);

print_r($parsedFiled);

fclose($logFile);

?>