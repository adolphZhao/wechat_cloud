<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WechatBindUrl;
use App\Services\API\PageViewService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PageViewController extends Controller
{
    protected $service;

    public function __construct(PageViewService $service)
    {
        $this->service = $service;
    }

    public function view(Request $request, $hash, $id)
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

        if (!preg_match('/MicroMessenger/i', $request->userAgent())) {
            $jumpHtml = sprintf($jumpHtml, 'https://www.tencent.com/');
            echo $jumpHtml;
            exit;
        }
        $guideSettings = \DB::select("select a.host_id,a.hits,b.hosts as domain from wechat_public_domain_states a join wechat_public_config_hosts b on a.host_id=b.id where a.status=0 and a.guide_status=1 order by a.hits asc limit 5");
        $time = date('YmdHis');
        $ip = $request->header('x-forwarded-for');
        $thash = base64_encode($ip);

        if (!count($guideSettings)) {
            return '可用域名池为空';
        }

        file_put_contents('/tmp/user-ip.log', $ip . ' ' . date('Ymd') . "\n", 8);
        $ridx = rand(1, count($guideSettings)) - 1;
        $guideSettings = $guideSettings[$ridx];

        if ($guideSettings && $guideSettings->domain && !preg_match('/redirect=1/', $request->getQueryString()) || ((intval($time) - intval($request->get('r', 0))) > 30 && $thash != $request->input('ob'))) {

            $rk = hash('CRC32', rand(1, 9999));
            $tk = sha1(microtime(true));
            $rv = base64_encode(md5(sha1(microtime(true))));


            $pathInfo = preg_replace("/$hash/", $rk, $request->getPathInfo());

            $jumpHtml = sprintf($jumpHtml, 'http://' . $guideSettings->domain . $pathInfo . "?ob=$thash&r=$time&token=$tk&$rk=$rv&redirect=1");

            echo $jumpHtml;
            exit;

        }
        return $this->service->render($id, $request->getHost());
    }

    public function getJsonjs()
    {
        /**
         * 热门搞笑小品V   gh_032228d5319a
         * 小品排行榜 gh_63f72d1cfc6c
         * 广场舞排行榜 gh_95635393f092
         * 甜美好情歌 gh_c5a1f8bfbbbe
         * 搞笑视频汇 gh_4d5df930b22c
         * 小品二人转大全 gh_119a4cd854df
         * 醉美情歌台 gh_f3bc241774bd
         * 农村牛人 gh_72a6df3c25cd
         * 最新广场舞V gh_cc5fd78b01a9
         */
        //   $jsonjs = file_get_contents(resource_path('scripts/sojson.src.js'));
        return sprintf('<script src="%s"></script>', '/redpkg-site/js/sojson.js');
    }

    public function firstChannel(Request $request)
    {

        $statMap = [
            'www.881088.com.cn' => '<script src="https://s11.cnzz.com/z_stat.php?id=1261790255&web_id=1261790255" language="JavaScript"></script>',
            'www.880788.com.cn' => '<script src="https://s13.cnzz.com/z_stat.php?id=1272878748&web_id=1272878748" language="JavaScript"></script>',
            'www.dqddc.com.cn' => '<script src="https://s13.cnzz.com/z_stat.php?id=1272880494&web_id=1272880494" language="JavaScript"></script>'
        ];
        $host = $request->getHost();

        @header('Content-Type:text/html');

        $template = file_get_contents(resource_path('templates/first.template'));

        $hosts = explode(',', env('INDJ_HOST', ''));

        $switchOn = false;

        if (in_array($host, $hosts)) {
            $switchOn = true;
        }


        $template = preg_replace('/{statScript}/', array_get($statMap, $host, ''), $template);


        $template = preg_replace('/{dynamicScript}/', (env('INDJ_JS', false) && $switchOn) ? $this->getJsonjs() : '', $template);


        echo $template;
        exit;
    }

    public function test(Request $request)
    {
        header('Content-Type:application/json');
        $client = new Client();
        $response = $client->get(sprintf('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=%s', $request->header('remoteip')));
        $content = $response->getBody()->getContents();
        $coor = @json_decode($content);

        if ($coor->city === '吕梁') {
            echo '走入关注公众号,领取红包流程';
        } else {
            echo '您不是吕梁的用户无法领取红包';
        }
        exit;
    }
}