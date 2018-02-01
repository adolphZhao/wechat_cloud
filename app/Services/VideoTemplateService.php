<?php
namespace App\Services;

use App\Models\VideoTemplate;
use App\Repositories\VideoTemplateRepository;

class VideoTemplateService
{
    protected $repository;

    public function __construct(VideoTemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($vid)
    {
        $video = $this->repository->get($vid);

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

    public function getTitle($vid)
    {
        $prefix = '';
        $core = '';
        $suffix = '';
        $title = '';

        $vt = VideoTemplate::query()->where('video_id', $vid)->first();
        if ($vt) {
            $prefixs = explode("\n", $vt->prefix);
            $cores = explode("\n", $vt->core);
            $suffixs = explode("\n", $vt->suffix);
            if (count($prefixs) > 0) {
                $ridx = rand(0, count($prefixs) - 1);
                $prefix = $prefixs[$ridx];
            }
            if (count($cores) > 0) {
                $ridx = rand(0, count($cores) - 1);
                $core = $cores[$ridx];
            }
            if (count($suffixs) > 0) {
                $ridx = rand(0, count($suffixs) - 1);
                $suffix = $suffixs[$ridx];
            }
            $title = preg_replace('/{prefix}/', $prefix, $vt->template);
            $title = preg_replace('/{core}/', $core, $title);
            $title = preg_replace('/{suffix}/', $suffix, $title);
        }
        return $title;
    }
}