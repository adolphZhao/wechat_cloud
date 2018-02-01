<?php
namespace App\Http\Controllers;

use App\Services\VideoTemplateService;
use Illuminate\Http\Request;


class VideoTemplateController extends Controller
{
    protected $videoService;

    public function __construct(VideoTemplateService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function get($vid)
    {
        return $this->videoService->get($vid);
    }

    public function delete($vid)
    {
        return $this->videoService->delete($vid);
    }

    public function update(Request $request, $vid)
    {
        $attributes = $request->all();
        return $this->videoService->update($vid, $attributes);
    }

    public function create(Request $request)
    {
        $attributes = $request->all();
        return $this->videoService->create($attributes);
    }

    public function all()
    {
        $data = $this->videoService->all();
        return definedResponse($data);
    }
}