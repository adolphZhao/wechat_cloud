<?php
namespace App\Services\API;

use App\Supports\TextRender\ITextRender;
use App\Supports\TextRender\RenderFactory;
use Carbon\Carbon;

class PageViewService
{
    public function render($format)
    {
        /**
         * @var ITextRender $render
         */
        $render = RenderFactory::create($format);

        return $render->renderText();
    }
}