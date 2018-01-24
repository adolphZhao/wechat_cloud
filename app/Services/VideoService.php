<?php
namespace App\Services;

use App\Repositories\VideoRepository;

class VideoService
{
    protected $repository;

    public function __construct(VideoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($vid)
    {
        $video = $this->repository->get($vid);

        if ($video) {
            $config = $this->repository->getConfig($vid);
            $video['config'] = $config;
        }
        return $video;
    }

    public function delete($vid)
    {
        return $this->repository->delete($vid);
    }

    public function update($vid, $attributes)
    {
        return \DB::transaction(function () use ($vid, $attributes) {
            return $this->repository->update($vid, $attributes);
        });
    }

    public function create($attributes)
    {
        return \DB::transaction(function () use ($attributes) {
            return $this->repository->create($attributes);
        });
    }

    public function all()
    {
        $videos = $this->repository->all();

        foreach ($videos as $idx => $video) {
            $config = $this->repository->getConfig($video['id']);
            $videos[$idx]['config'] = $config;
        }
        return $videos;
    }
}