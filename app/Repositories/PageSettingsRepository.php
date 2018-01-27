<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/26
 * Time: 上午11:22
 */

namespace App\Repositories;


use App\Models\PageSettings;
use Carbon\Carbon;

class PageSettingsRepository
{
    public function get($vid)
    {
        $video = PageSettings::query()->where('id', $vid)->first();
        return $video;
    }

    public function delete($vid)
    {
        return PageSettings::query()->where('id', $vid)->delete();
    }

    public function update($vid, $attributes)
    {
        return PageSettings::query()->where('id', $vid)->update($attributes);
    }

    public function create($attributes)
    {
        $settings = PageSettings::create($attributes);

        return $settings;
    }

    public function all()
    {
        $settings = PageSettings::query()->get()->toArray();

        return $settings;
    }

    public function publish($vid)
    {
        PageSettings::query()->update(['published'=> 0]);
        return PageSettings::query()->where('id', $vid)->update(['published'=> 1,'publish_time'=>Carbon::now()->format('y-m-d H:i:s')]);
    }
}