<?php
namespace App\Services;

use App\Repositories\SettingsRepository;

class SettingsService
{
    protected $repository;

    public function __construct(SettingsRepository $repository)
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

        return $settings;
    }
}