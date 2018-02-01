<?php

if (preg_match('/(\.html|\.xhtml|\.dhtml|\.shtml)/', getenv('REQUEST_URI')) && isset($_GET['vid'])) {
    $cHost = getenv('HTTP_HOST');
    $hash = hash('CRC32', $cHost . date('YmdHi'));
    $host = sprintf('http://%s/rss/view-%s-%s.htm', $cHost, $hash, $_GET['vid']);

    header('HTTP/1.1 303 See Other');
    header('Location: ' . 'http://so.le.com/s3/?to=' . $host);
    exit;
}

if (!preg_match('/ob=/', getenv('QUERY_STRING')) && preg_match('/\.htm/', getenv('REQUEST_URI'))) {
    header('HTTP/1.1 200');
    $rk = base64_encode(hash('CRC32', rand(1, 9999)));
    $tk = sha1(microtime(true));
    $rv = base64_encode(md5(sha1(microtime(true))));
    $time = date('YmdHis');
    $hash = base64_encode($time);
    header('Location: ' . $host . "?ob=$hash&r=$time&token=$tk&$rk=$rv");

} elseif (preg_match('/ob=([a-zA-Z0-9=]+)/', getenv('QUERY_STRING'), $matches)) {
    $date = @base64_decode($matches[1]);
    if (intval(date('YmdHis')) - intval($date) > 60) {
        header('HTTP/1.1 200');
        $rk = base64_encode(hash('CRC32', rand(1, 9999)));
        $tk = sha1(microtime(true));
        $rv = base64_encode(md5(sha1(microtime(true))));
        $time = date('YmdHis');
        $hash = base64_encode($time);
        header('Location: ' . $host . "?ob=$hash&r=$time&token=$tk&$rk=$rv");
    }
}

require_once __DIR__ . '/public/index.php';


