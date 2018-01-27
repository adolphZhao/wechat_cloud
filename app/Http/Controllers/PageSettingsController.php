<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/26
 * Time: 上午11:21
 */

namespace App\Http\Controllers;


use App\Services\PageSettingsService;
use Illuminate\Http\Request;

class PageSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(PageSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function get($vid)
    {
        return $this->settingsService->get($vid);
    }

    public function delete($vid)
    {
        return $this->settingsService->delete($vid);
    }

    public function update(Request $request, $vid)
    {
        $attributes = $request->all();
        return $this->settingsService->update($vid, $attributes);
    }

    public function create(Request $request)
    {
        $attributes = $request->all();
        return $this->settingsService->create($attributes);
    }

    public function all()
    {
        $data = $this->settingsService->all();
        return definedResponse($data);
    }

    public function publish($vid)
    {
        $data = $this->settingsService->publish($vid);
        return definedResponse($data);
    }
}