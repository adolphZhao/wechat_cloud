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

        $videoTable = (new Video())->getTable();
        $pageSettingsTable = (new PageSettings())->getTable();

        $video = PageSettings::query()
            ->join($videoTable, 'video_id', '=', "$videoTable.id")
            ->where("$pageSettingsTable.published", 1)
            ->select(["$videoTable.*"])
            ->first();
        if ($video) {
            foreach ($domains as $idx => $domain) {
                $d = array_get($domain, 'domain');
                $host = sprintf('http://%s/public/view-%s.shtml', $d, $video->code);
                $imgdata = $this->repository->getQrCode($host);
                array_set($domains, "$idx.imgdata", $imgdata);
                array_set($domains[$idx], 'url', $host);
            }
        }

        return $domains;
    }
}