<?php
namespace App\Supports\TextRender;

use Carbon\Carbon;
use Illuminate\Cache\MemcachedStore;
use Illuminate\Support\Facades\Cache;

class HTMLRender implements ITextRender
{
    public function renderText()
    {
        \Cache::put('PAGE_VIEW_CODE',Carbon::now()->getTimestamp(),3600*1000);
        $html =  \Cache::get('PAGE_VIEW_CODE');
        echo  $html;
        exit;
    }
}