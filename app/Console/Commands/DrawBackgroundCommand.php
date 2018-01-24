<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class DrawBackgroundCommand extends Command
{
    protected $signature = "make:background {w} {h}";

    public function handle()
    {
        $w = $this->argument('w');
        $h = $this->argument('h');
        $gd = gd_info();
        if ($gd) {
            $img = imagecreate($w, $h);
//            $font = imageloadfont(resource_path('PingFang.ttc'));
            $white = imagecolorallocate($img, 255, 255, 255);
            $black = imagecolorallocate($img, 0, 0, 0);

            imagefill($img, 0, 0, $white);
            $fontSize = 18;
            $text = "实拍上海一妈妈带儿子吃饭被打，爸爸拿出100w砸饭店...";
            $fontWidth = imagefontwidth($fontSize);//获取文字宽度
            $length = ceil(($w - 30) / $fontWidth);
            $texts = str_split($text, $length);
//            imagepstext($img, "这里会放一个很长的台头这里会放一个很长的台头这里会放一个很长的台头", $font, 12, $black, $white, 20, 20);
            foreach ($texts as $idx => $text) {
                imagettftext($img, $fontSize, 0, 30, ($idx + 1) * 30, $black, resource_path('PingFang.ttc'), $text);
            }

            imagettftext($img, $fontSize, 0, 30, ($idx + 1) * 30, $black, resource_path('PingFang.ttc'), $text);
            imagerectangle($img, 30, ($fontWidth * 2 + 50), $w - 30, ($fontWidth * 2 + 50) + 400, $black);
            imagefill($img, 31, ($fontWidth * 2 + 51), $black);

            @mkdir(storage_path('images'));
            imagepng($img, storage_path('images/background-image.png'));
        }
        $this->info($gd['GD Version']);
    }
}