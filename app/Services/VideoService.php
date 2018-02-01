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

        return $video;
    }

    public function getByMapId($vid)
    {
        $video = $this->repository->getByMapId($vid);

        return $video;
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
        $videos = $this->repository->all();

        return $videos;
    }
}