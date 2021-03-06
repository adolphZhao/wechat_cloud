<?php
namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
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
}