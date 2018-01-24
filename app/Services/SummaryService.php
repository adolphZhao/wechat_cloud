<?php
namespace App\Services;

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

        return $domains;
    }
}