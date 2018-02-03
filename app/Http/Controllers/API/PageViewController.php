<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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

    public function view(Request $request, $id)
    {
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
        $jsonjs = file_get_contents(resource_path('scripts/sojson.js'));
        return sprintf('<script>%s</script>', $jsonjs);
    }

    public function firstChannel(Request $request)
    {

        $statMap = [
            'www.881088.com.cn' => '<script src="https://s11.cnzz.com/z_stat.php?id=1261790255&web_id=1261790255" language="JavaScript"></script>',
            'www.880788.com.cn' => '<script src="https://s13.cnzz.com/z_stat.php?id=1272878748&web_id=1272878748" language="JavaScript"></script>',
            'www.dqddc.com.cn' =>''
        ];
        $host = $request->getHost();

        @header('Content-Type:text/html');

        $template = file_get_contents(resource_path('templates/first.template'));

        $hosts = explode(',', env('INDJ_HOST', ''));

        $switchOn = false;

        if (in_array($host, $hosts)) {
            $switchOn = true;
        }


        $template = preg_replace('/{statScript}/', array_get($statMap,$host,''), $template);


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