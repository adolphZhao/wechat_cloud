<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/26
 * Time: 上午11:21
 */

namespace App\Services;


use App\Repositories\PageSettingsRepository;
use App\Repositories\VideoRepository;

class PageSettingsService
{
    protected $repository;
    protected $videoRepository;

    public function __construct(PageSettingsRepository $repository, VideoRepository $videoRepository)
    {
        $this->repository = $repository;
        $this->videoRepository = $videoRepository;
    }

    public function get($vid)
    {
        $settings = $this->repository->get($vid);

        return $settings;
    }

    public function delete($vid)
    {
        return $this->repository->delete($vid);
    }

    public function update($vid, $attributes)
    {
        return $this->repository->update($vid, $attributes);
    }

    public function create($attributes)
    {
        return $this->repository->create($attributes);
    }

    public function all()
    {
        $settings = $this->repository->all();
        foreach ($settings as $idx => $setting) {
            array_set($settings, "$idx.ad_top_show", array_get($setting, 'ad_top_show') == 1 ? true : false);
            array_set($settings, "$idx.ad_bottom_show", array_get($setting, 'ad_bottom_show') == 1 ? true : false);
            array_set($settings, "$idx.ad_back_show", array_get($setting, 'ad_back_show') == 1 ? true : false);
            array_set($settings, "$idx.ad_author_show", array_get($setting, 'ad_author_show') == 1 ? true : false);
            array_set($settings, "$idx.ad_original_show", array_get($setting, 'ad_original_show') == 1 ? true : false);
        }

        return $settings;
    }

    public function publish($vid)
    {
        $settings = $this->repository->get($vid);
        $this->repository->publish($vid);
        \Cache::put('PAGE_TEMPLATE_' . $settings->video_id, @json_encode($settings), 3600 * 24 * 7);
        return $settings->description;
    }
}