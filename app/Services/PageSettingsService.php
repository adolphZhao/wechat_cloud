<?php
namespace App\Services;

use App\Models\Video;
use App\Repositories\PageSettingsRepository;


class PageSettingsService
{
    protected $repository;

    public function __construct(PageSettingsRepository $repository)
    {
        $this->repository = $repository;
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
        $status = $this->repository->publish($vid);
        $this->clearCachedSettings();
        return $status ? 'success' : 'failure';
    }

    protected function clearCachedSettings()
    {
        $videos = Video::query()->get();

        if ($videos) {
            foreach ($videos as $video) {
                \Cache::forget(env('TEMPLATE_CACHE_PREFIX', 'PAGE_TEMPLATE_') . $video->code);
            }
        }
    }
}