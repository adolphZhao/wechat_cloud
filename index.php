<?php

if(preg_match('/(\.html|\.xhtml|\.dhtml|\.shtml)/',getenv('REQUEST_URI'))&&isset($_GET['vid'])){
    $cHost = getenv('HTTP_HOST');
    $hash =  hash('CRC32',$cHost);
    $host = sprintf('http://%s/rss/view-%s-%s.htm', $cHost, $hash, $_GET['vid']);

    header('HTTP/1.1 303 See Other');
    header('Location: ' . 'http://so.le.com/s3/?to=' . $host);
    exit;
}

require_once __DIR__.'/public/index.php';


