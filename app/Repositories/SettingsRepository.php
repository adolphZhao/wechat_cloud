<?php
namespace App\Repositories;

use App\Models\Settings;

class SettingsRepository
{
    public function get($vid)
    {
        $video = Settings::query()->where('id', $vid)->first();
        return $video;
    }

    public function delete($vid)
    {
        return Settings::query()->where('id', $vid)->delete();
    }

    public function update($vid, $attributes)
    {
        return Settings::query()->where('id', $vid)->update($attributes);
    }

    public function create($attributes)
    {
        $settings = Settings::create($attributes);

        return $settings;
    }

    public function all()
    {
        $settings = Settings::query()->get()->toArray();
        return $settings;
    }
}