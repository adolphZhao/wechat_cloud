<?php
namespace App\Services;

use App\Repositories\WechatSettingsRepository;

class WechatSettingsService
{
    protected $repository;

    public function __construct(WechatSettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($vid)
    {
        $settings = $this->repository->get($vid);
        $hosts = $this->repository->getConfig($vid);
        array_set($settings, 'config', $hosts);

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
            $hosts = $this->repository->getConfig(array_get($setting, 'id', 0));
            array_set($settings[$idx], 'config', $hosts);
        }

        return $settings;
    }
}