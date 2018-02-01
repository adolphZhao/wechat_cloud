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

    public function test(Request $request)
    {
        echo $request->getHost();
        echo md5($request->getHost());

        if(\Cache::get('DOMAIN_HITS_'.md5($request->getHost()))==null){
            \Cache::put('DOMAIN_HITS_'.md5($request->getHost()),0,3600*24);
        }
        echo \Cache::increment('DOMAIN_HITS_'.md5($request->getHost()));

        exit;
    }
}