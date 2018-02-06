<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class DrawBackgroundCommand extends Command
{
    protected $signature = "make:background {w} {h}";

    public function handle()
    {
        $img = Image::canvas(400, 700, '#eeeeee');
        $jiyin = Image::make(resource_path('images/jiyin.png'));
        $jiyin->resize(400,700);
        $img->insert($jiyin,'left-top',0,0);
        $car = Image::make(resource_path('images/audi.png'));
        $car->resize(150,100);
        $img->insert($car,'top-right',20,20);
        $img->text('职业预测',20,40,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(26);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
           // $font->angle(45);
        });

        $img->text('根据您的基因预测, 您的宝宝未来成就不可限量',20,140,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来职业是: ',20,180,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('律师',180,175,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来年薪是:',20,220,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('100-200万',180,215,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来座驾是:',20,260,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('奥迪',180,255,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来学校是:',20,300,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('清华大学',180,295,function($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $qrcode = app(BaconQrCodeGenerator::class);

       $qrcode =  $qrcode->format('png')->size(120)->margin(1)->generate('http://www.baidu.com');

        $img->insert($qrcode,'bottom-right',20,20);
        $img->save(storage_path('audi.png'));
    }
}