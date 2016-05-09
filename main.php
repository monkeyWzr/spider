<?php
// include_once('Spider.php');

// $spider = new Spider();
// $spider->setURL("www.zimuzu.tv");
// $spider->addContentTypeReceiveRule("#text/html#");


// $spider->go();
// It may take a whils to crawl a site ...
set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("PHPCrawl_083/libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method
// 继承基类并重写handleDocumentInfo()方法
class MyCrawler extends PHPCrawler
{
  function handleDocumentInfo(PHPCrawlerDocumentInfo $DocInfo)
  {
    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    // 检测输出的换行格式（命令行模式为"\n"，否则为"<br>"）
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";

    // Print the URL and the HTTP-status-Code
    echo "Page requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;

    // Print the refering URL
    echo "Referer-page: ".$DocInfo->referer_url.$lb;

    // Print if the content of the document was be recieved or not
    if ($DocInfo->received == true)
      echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
    else
      echo "Content not received".$lb;

    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example

    echo $lb;

    flush();
  }
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process.

$crawler = new MyCrawler();

// URL to crawl
$crawler->setURL("www.zimuzu.tv");

// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
$crawler->setTrafficLimit(1000 * 1024);

// Thats enough, now here we go
// $crawler->go();

// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
// $report = $crawler->getProcessReport();

// if (PHP_SAPI == "cli") $lb = "\n";
// else $lb = "<br />";

// echo "Summary:".$lb;
// echo "Links followed: ".$report->links_followed.$lb;
// echo "Documents received: ".$report->files_received.$lb;
// echo "Bytes received: ".$report->bytes_received." bytes".$lb;
// echo "Process runtime: ".$report->process_runtime." sec".$lb;
// $cookie_file = fopen('cookie.json') or die('No cookie file found!');
$cookie = json_decode(file_get_contents('cookie.json'));

var_dump($crawler);
