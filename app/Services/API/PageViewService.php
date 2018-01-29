<?php
namespace App\Services\API;


use App\Models\PageSettings;
use App\Models\Settings;
use App\Models\Video;
use App\Models\VideoConfig;
use App\Repositories\PageSettingsRepository;
use App\Repositories\WechatSettingsRepository;
use App\Repositories\VideoRepository;
use App\Supports\TextCompiler;

class PageViewService
{
    protected $repository;
    protected $videoRepository;
    protected $settingsRepository;

    public function __construct(PageSettingsRepository $repository, VideoRepository $videoRepository, WechatSettingsRepository $settingsRepository)
    {
        $this->repository = $repository;
        $this->videoRepository = $videoRepository;
        $this->settingsRepository = $settingsRepository;
    }

    public function render($code)
    {
        $html = $this->comparedHTML($code);
        header('Content-Type: text/html');
        echo $html;
        exit;
    }

    public function comparedHTML($code)
    {
        if ($html = \Cache::get(env('TEMPLATE_CACHE_PREFIX', 'PAGE_TEMPLATE_') . $code)) {
            return $html;
        }
        $videoTable = (new Video())->getTable();
        $pageSettingsTable = (new PageSettings())->getTable();
        $settings = PageSettings::query()
            ->join($videoTable, 'video_id', '=', "$videoTable.id")
            ->where("$videoTable.code", $code)
            ->where("$pageSettingsTable.published", 1)
            ->select(["$pageSettingsTable.*"])
            ->first();

        if ($settings) {

            $html = file_get_contents(resource_path('templates/base.template'));

            $html = $this->hostRender($html);
            $html = $this->reportRender($html, $settings->report);
            $html = $this->adTopRender($html, $settings->ad_top_show);
            $html = $this->adBottomRender($html, $settings->ad_bottom_show);
            $html = $this->adAuthorRender($html, $settings->ad_author_show);
            $html = $this->adOriginalRender($html, $settings->ad_original_show);
            $html = $this->base64ScriptRender($html, $settings->video_id, env('USE_ORIGINAL_SCRIPT', false));

            \Cache::put(env('TEMPLATE_CACHE_PREFIX', 'PAGE_TEMPLATE_') . $code, $html, 3600 * 24 * 7);
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

    protected function adOriginalRender($html, $adOriginalShow = false)
    {
        $adOriginal = Settings::query()->where('position', 4)->first();
        if ($adOriginalShow && $adOriginal) {
            $html = preg_replace('/{original}/', sprintf('<a href="%s"><span class="born" >%s</span></a>', $adOriginal->url, $adOriginal->title), $html);
        } else {
            $html = preg_replace('/{original}/', '', $html);
        }

        return $html;
    }

    protected function base64ScriptRender($html, $vid, $original = false, $filename = 'wechat-1.0.0.js')
    {
        $videoTable = (new Video())->getTable();
        $videoConfigTable = (new VideoConfig())->getTable();
        $pageSettingsTable = (new PageSettings())->getTable();

        $dynamicData = Video::query()
            ->join($videoConfigTable, $videoConfigTable . '.video_id', '=', $videoTable . '.id')
            ->where($videoTable . '.id', $vid)
            ->select([
                $videoTable . '.code',
                $videoTable . '.stop_time',
                $videoConfigTable . '.title',
                $videoConfigTable . '.image'
            ])
            ->first();

        $videos = Video::query()
            ->join($videoConfigTable, $videoConfigTable . '.video_id', '=', $videoTable . '.id')
            ->join($pageSettingsTable, "$pageSettingsTable.video_id", '=', "$videoTable.id")
            ->whereNotIn($videoTable . '.id', [$vid])
            ->where("$pageSettingsTable.published", 1)
            ->select([
                $videoTable . '.code',
                $videoTable . '.stop_time',
                $videoConfigTable . '.title',
                $videoConfigTable . '.image'
            ])
            ->orderBy('weight', 'DESC')
            ->forPage(1, 3)
            ->get()
            ->toArray();

        $hosts = $this->settingsRepository->getHosts();
        $hosts = array_map(function ($host) {
            return ['hosts' => sprintf('http://%s/public/', $host['hosts'])];
        }, $hosts);

        $dynamicData->hosts = $hosts;
        $dynamicData->shareVideos = $videos;

        if ($original) {
            $dynamicScript = file_get_contents(resource_path('scripts/' . $filename));
            $html = preg_replace('/{dynamicScript}/', sprintf('<script>var configData =%s;%s</script>', $dynamicData, $dynamicScript), $html);
            return $html;
        }
        $dynamicScript = TextCompiler::scriptToBase64(resource_path('scripts/' . $filename));

        $dynamicData = TextCompiler::dataToBase64($dynamicData);
        $html = preg_replace('/{dynamicScript}/', sprintf('<img id="dys" src="%s" alt="" /><img id="dyd" src="%s" alt="" />', $dynamicScript, $dynamicData), $html);
        return $html;
    }
}