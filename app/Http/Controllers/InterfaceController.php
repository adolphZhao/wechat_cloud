<?php
namespace App\Http\Controllers;


use App\Services\VideoService;
use App\Services\WechatSettingsService;
use Illuminate\Http\Request;

class InterfaceController extends Controller
{
    protected $wechatSettingsService;
    protected $videoService;

    public function __construct(WechatSettingsService $wechatSettingsService, VideoService $videoService)
    {
        $this->wechatSettingsService = $wechatSettingsService;
        $this->videoService = $videoService;
    }

    public function enter(Request $request)
    {
        if (!($hosts = @unserialize(\Cache::get('HOSTS')))) {
            $hosts = $this->wechatSettingsService->getHosts();
            \Cache::put('HOSTS', @serialize($hosts), 3600 * 24);
        }

        if (!empty($hosts)) {
            $total = count($hosts);
            $ridx = rand(0, $total - 1);
            $jump = array_get($hosts, "$ridx.hosts");
            if ($vid = $request->input('vid')) {
                $hash = hash('CRC32', $jump . $vid . date('YmdH'));
                $video = $this->videoService->getByMapId($vid);

                if ($video) {
                    $host = sprintf('http://%s/rss/view-%s-%s.jsp', $jump, $hash, $video->code);
                }

                header('HTTP/1.1 303 See Other');
                header('Location: ' . 'http://so.le.com/s3/?to=' . $host);
            }
        }
    }
}