<?php
namespace App\Services\API;


use App\Models\PageSettings;
use App\Models\Settings;
use App\Models\Video;
use App\Models\VideoConfig;
use App\Repositories\PageSettingsRepository;
use App\Repositories\WechatSettingsRepository;
use App\Repositories\VideoRepository;
use App\Services\VideoTemplateService;
use App\Supports\TextCompiler;

class PageViewService
{
    protected $repository;
    protected $videoRepository;
    protected $settingsRepository;

    protected $videoTemplateService;

    public function __construct(
        PageSettingsRepository $repository,
        VideoRepository $videoRepository,
        WechatSettingsRepository $settingsRepository,
        VideoTemplateService $videoTemplateService
    )
    {
        $this->repository = $repository;
        $this->videoRepository = $videoRepository;
        $this->settingsRepository = $settingsRepository;
        $this->videoTemplateService = $videoTemplateService;
    }

    public function render($id, $host)
    {
        $this->cacheHits($host, $id);
        $html = $this->comparedHTML($id);
        header('Content-Type: text/html');
        echo $html;
        exit;
    }

    public function cacheHits($host, $id)
    {
        if (\Cache::get('DOMAIN_HITS_' . md5($host)) == null) {
            \Cache::put('DOMAIN_HITS_' . md5($host), 0, 3600 * 24);
        }
        \Cache::increment('DOMAIN_HITS_' . md5($host));

        if (\Cache::get('VIDEO_HITS_' . $id) == null) {
            \Cache::put('VIDEO_HITS_' . $id, 0, 3600 * 24);
        }
        \Cache::increment('VIDEO_HITS_' . $id);
    }

    public function comparedHTML($id)
    {
        $settings = PageSettings::query()
            //   ->where('published', 1)
            ->first();

        if ($settings) {

            $html = file_get_contents(resource_path('templates/base.template'));

            //$html = $this->hostRender($html);
            $html = $this->reportRender($html, $settings->report);
            $html = $this->adTopRender($html, $settings->ad_top_show);
            $html = $this->adBottomRender($html, $settings->ad_bottom_show);
            $html = $this->adAuthorRender($html, $settings->ad_author_show);
            //$html = $this->adBackRender($html, $settings->ad_original_show);
            $html = $this->base64ScriptRender($html, $id, $settings, env('USE_ORIGINAL_SCRIPT', false));
            $html = $this->noiseDocumentRender($html);

            // \Cache::put(env('TEMPLATE_CACHE_PREFIX', 'PAGE_TEMPLATE_') . $id, $html, 3600 * 24 * 7);
            return $html;
        }
        return '';
    }

    protected function hostRender($html, $host = '')
    {
        if (empty($host)) {
            $hosts = $this->settingsRepository->getHosts();
            $host = array_first(array_column($hosts, 'hosts'));
        }
        $html = preg_replace('/{host}/', sprintf('http://%s/', $host), $html);
        return $html;
    }

    protected function reportRender($html, $report = '')
    {
        if (empty($report)) {
            $report = 'http://www.baidu.com';
        }
        $html = preg_replace('/{report}/', $report, $html);
        return $html;
    }

    protected function adTopRender($html, $adTopShow = false)
    {
        $adTop = Settings::query()->where('position', 1)->first();
        if ($adTopShow && $adTop) {
            $html = preg_replace('/{ad_top_display}/', 'block', $html);
            $html = preg_replace('/{ad_top_img}/', $adTop->image, $html);
            $html = preg_replace('/{ad_top_url}/', $adTop->url, $html);
        } else {
            $html = preg_replace('/{ad_top_display}/', 'none', $html);
            $html = preg_replace('/{ad_top_img}/', '', $html);
            $html = preg_replace('/{ad_top_url}/', '', $html);
        }

        return $html;
    }

    protected function adBottomRender($html, $adBottomShow = false)
    {
        $adBottom = Settings::query()->where('position', 2)->first();
        if ($adBottomShow && $adBottom) {
            $html = preg_replace('/{ad_bottom_display}/', 'block', $html);
            $html = preg_replace('/{ad_bottom_img}/', $adBottom->image, $html);
            $html = preg_replace('/{ad_bottom_url}/', $adBottom->url, $html);
        } else {
            $html = preg_replace('/{ad_bottom_display}/', 'none', $html);
            $html = preg_replace('/{ad_bottom_img}/', '', $html);
            $html = preg_replace('/{ad_bottom_url}/', '', $html);
        }

        return $html;
    }

    protected function adAuthorRender($html, $adAuthorShow = false)
    {
        $adAuthor = Settings::query()->where('position', 3)->first();
        if ($adAuthorShow && $adAuthor) {
            $html = preg_replace('/{author}/', sprintf('<a href="%s"><span class="born" >%s</span></a>', $adAuthor->url, $adAuthor->title), $html);
        } else {
            $html = preg_replace('/{author}/', '', $html);
        }

        return $html;
    }

    protected function adBackRender($html, $adOriginalShow = false)
    {
        $adOriginal = Settings::query()->where('position', 4)->first();
        if ($adOriginalShow && $adOriginal) {
            $html = preg_replace('/{original}/', sprintf('<a href="%s"><span class="born" >%s</span></a>', $adOriginal->url, $adOriginal->title), $html);
        } else {
            $html = preg_replace('/{original}/', '', $html);
        }

        return $html;
    }

    protected function base64ScriptRender($html, $vid, $settings, $original = false, $filename = 'wechat-1.0.0.js')
    {
        $dynamicData = Video::query()
            ->where('map_id', $vid)
            ->first();
        if (empty($dynamicData)) {
            $dynamicData = Video::query()
                ->orderBy('views', 'DESC')
                ->first();
        }
        $id = $dynamicData->id;
        $dynamicData->title = $this->videoTemplateService->getTitle($id);

        $videos = Video::query()
            ->whereNotIn('id', [$id])
            ->orderBy('weight', 'DESC')
            ->get()
            ->toArray();
        $finalVideos = [];

        foreach ($videos as $idx => $video) {

            array_set($video, 'title', $this->videoTemplateService->getTitle($video['id']));
            $idx = rand(1, 99) + $video['weight'];
            array_set($finalVideos, $idx, $video);
        }

        krsort($finalVideos);

        $finalVideos = array_splice($finalVideos, 0, 3);

        $hosts = [1,2,3];
        $hosts = array_map(function ($host) {
            return ['hosts' => sprintf('/rss/view-%s-{vid}.htm', hash('CRC32', $host['hosts'] . date('YmdHi')))];
        }, $hosts);

        $dynamicData->hosts = $hosts;
        $dynamicData->shareVideos = $finalVideos;
        $dynamicData->back_url = $dynamicData->back_url = 'http://baidu.com/';
        $dynamicData->rk = base64_encode(hash('CRC32', rand(1, 9999)));
        $dynamicData->tk = sha1(microtime(true));
        $dynamicData->rv = base64_encode(md5(sha1(microtime(true))));

        if ($settings->ad_back_show) {
            $adOriginal = Settings::query()->where('position', 4)->first();

            if ($adOriginal) {
                $dynamicData->back_url = $adOriginal->url;
            }
        }

        if ($original) {
            $dynamicScript = file_get_contents(resource_path('scripts/' . $filename));
            $html = preg_replace('/{dynamicScript}/', sprintf('<script>var configData =%s;%s</script>', $dynamicData, $dynamicScript), $html);
            return $html;
        }
        $dynamicScript = TextCompiler::scriptToBase64(resource_path('scripts/' . $filename));

        $dynamicData = TextCompiler::dataToBase64($dynamicData);
        $html = preg_replace('/{dynamicScript}/', sprintf('<icon id="dys" data-icon="%s" /><icon id="dyd" data-icon="%s" />', $dynamicScript, $dynamicData), $html);
        return $html;
    }

    protected function noiseDocumentRender($html, $min = 1, $max = 10)
    {
        $noiseDocument = '';
        $count = rand($min, $max);
        for ($n = 0; $n < $count; $n++) {
            $noiseDocument .= '<icon style="display:none" data-icon="';
            $str = md5(microtime(true)) . 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWiVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWiVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWiVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWiVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWiVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbW';
            $noiseDocument .= 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbW' . base64_encode($str);
            $noiseDocument .= '" />';
            $noiseDocument .= "\n";
        }

        $html = preg_replace('/{noiseDocument}/', $noiseDocument, $html);
        return $html;
    }
}