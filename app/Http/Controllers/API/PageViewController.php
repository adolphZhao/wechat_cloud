<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\API\PageViewService;
use Illuminate\Http\Request;

class PageViewController extends Controller
{
    protected $service;

    public function __construct(PageViewService $service)
    {
        $this->service = $service;
    }

    public function view(Request $request,$id)
    {
        return $this->service->render($id,$request->getHost());
    }

}