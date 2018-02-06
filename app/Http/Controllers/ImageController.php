<?php
namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function draw(Request $request)
    {
        return $this->imageService->draw($request->getHost());
    }

    public function drinkTicket(Request $request)
    {
        $name = $request->input('name');
        $host = $request->getHost();
        return $this->imageService->drawDrinkTicket($host, $name);
    }

    public function fightTicket(Request $request)
    {
        $loser = $request->input('loser');
        $winer = $request->input('winer');
        $host = $request->getHost();
        return $this->imageService->fightTicket($host, $loser, $winer);
    }

    public function compensateTicket(Request $request)
    {
        $name = $request->input('name');
        $host = $request->getHost();
        return $this->imageService->compensateTicket($host, $name);
    }

    public function psychosisTicket(Request $request)
    {
        $name = $request->input('name');
        $age = $request->input('age');
        $sex = $request->input('sex');
        $host = $request->getHost();
        return $this->imageService->psychosisTicket($host, $name, $age, $sex);
    }
}