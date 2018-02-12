<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\WechatSettingsService;
use Illuminate\Http\Request;

class JumpController extends Controller
{
    public function jumpTo(WechatSettingsService $service, Request $request)
    {
        $jumpHtml = <<<EOF
<html>
    <head>
    <meta name="referrer" content="never">
    <meta http-equiv="refresh" content="0;url=%s">
    </style>
    </head>
    <body></body>
</html>
EOF;
        $hosts = $service->getHosts();
        $hosts = array_values(array_column($hosts, 'hosts'));
        array_unshift($hosts, 'localhost');
        $domain = $request->input('to');

        if (!in_array($request->getHost(), $hosts)) {
            throw new \Exception('', 404);
        }
        if (empty($domain)) {
            $domain = 'www.baidu.com';
        }
        if (!starts_with('http://', $domain) && !starts_with('https://', $domain)) {
            $domain = 'http://' . $domain;
        }
        $jumpHtml = sprintf($jumpHtml, $domain);
        dd($jumpHtml);
        echo $jumpHtml;

        exit;
    }
}