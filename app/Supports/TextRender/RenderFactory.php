<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/26
 * Time: 下午8:44
 */

namespace App\Supports\TextRender;


class RenderFactory
{
    public static function create($format)
    {
        $format = strtoupper($format);
        if (in_array($format, ['HTML', 'JSON', 'DHTML'])) {
            $render = app(sprintf('App\\Supports\\TextRender\\%sRender', $format));
            return $render;
        }
        throw new \Exception('unsupports render format.', 422);
    }
}