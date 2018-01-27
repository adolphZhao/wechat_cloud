<?php
namespace App\Services\API;

class PageViewService
{
    public function render($id)
    {
        $html =  \Cache::get('PAGE_TEMPLATE_'.$id);
        header('Content-Type: text/html');
        echo  $html;
        exit;
    }
}