<?php
namespace App\Services;

use App\Models\PageSettings;
use App\Models\Video;
use App\Repositories\SummaryRepository;

class SummaryService
{
    protected $repository;

    public function __construct(SummaryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($vid)
    {
        $domain = $this->repository->get($vid);
        return $domain;
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
        $domains = $this->repository->all();

        $video = Video::query()->first();
        if ($video) {
            foreach ($domains as $idx => $domain) {
                $d = array_get($domain, 'domain');
                $host = sprintf('http://%s/rss/view-hash-%s.htm', $d, $video->map_id);
                $imgdata = $this->repository->getQrCode($host);
                array_set($domains, "$idx.imgdata", $imgdata);
                array_set($domains[$idx], 'url', $host);
            }
        }

        return $domains;
    }
}