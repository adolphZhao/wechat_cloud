<?php
namespace App\Repositories;

use App\Models\WechatBindUrl;
use App\Models\WechatSettings;

class WechatSettingsRepository
{
    public function get($pid)
    {
        $video = WechatSettings::query()->where('id', $pid)->first();
        return $video;
    }

    public function getConfig($pid)
    {
        $bindUrls = WechatBindUrl::query()->where('public_id', $pid)->get()->toArray();
        return $bindUrls;
    }

    public function getHosts()
    {
        $bindUrls = WechatBindUrl::query()->get()->toArray();
        return $bindUrls;
    }

    public function delete($pid)
    {
        WechatBindUrl::query()->where('id', $pid)->delete();
        return WechatSettings::query()->where('id', $pid)->delete();
    }

    public function update($pid, $attributes)
    {
        WechatBindUrl::query()->where('public_id', $pid)->delete();
        WechatBindUrl::query()->insert($this->fetchSettings($attributes, $pid));
        unset($attributes['bind_url']);
        return WechatSettings::query()->where('id', $pid)->update($attributes);
    }

    public function create($attributes)
    {
        $settings = WechatSettings::create($attributes);

        WechatBindUrl::query()->insert($this->fetchSettings($attributes, $settings->id));

        return $settings;
    }

    public function all()
    {
        $settings = WechatSettings::query()->get()->toArray();
        return $settings;
    }

    public function fetchSettings($attributes, $pid)
    {
        $titles = explode("\n", array_get($attributes, 'bind_url', ''));
        $inserted = [];

        foreach ($titles as $idx => $title) {
            $inserted[] = [
                'hosts' => $title,
                'public_id' => $pid
            ];
        }

        return $inserted;
    }

}