<?php
namespace App\Services\API;

class PageViewService
{
    public function render($code)
    {
        $html =  \Cache::get('PAGE_TEMPLATE_'.$code);
        header('Content-Type: text/html');
        echo  $html;
        exit;
    }
}