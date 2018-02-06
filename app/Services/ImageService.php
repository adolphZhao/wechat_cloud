<?php
namespace App\Services;

use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class ImageService
{
    protected $dataMap = [
        [
            'occupation' => '集团总裁',
            'car' => '劳斯莱斯',
            'income' => '5000-6000万',
            'img' => 'rolls-royce.png'
        ], [
            'occupation' => '外企高管',
            'car' => '保时捷',
            'income' => '200-500万',
            'img' => 'porsche.png'
        ], [
            'occupation' => '明星',
            'car' => '兰博基尼',
            'income' => '2000-5000万',
            'img' => 'lamborghini.png'
        ], [
            'occupation' => '科学家',
            'car' => '奥迪',
            'income' => '100-200万',
            'img' => 'audi.png'
        ], [
            'occupation' => '法官',
            'car' => '奔驰',
            'income' => '40-80万',
            'img' => 'benz.png'
        ], [
            'occupation' => '医学专家',
            'car' => '玛莎拉蒂',
            'income' => '200-500万',
            'img' => 'maserati.png'
        ], [
            'occupation' => '飞行员',
            'car' => '路虎',
            'income' => '200-400万',
            'img' => 'range.png'
        ], [
            'occupation' => '银行高管',
            'car' => '宝马',
            'income' => '60-150万',
            'img' => 'bmw.png'
        ], [
            'occupation' => '大导演',
            'car' => '宾利',
            'income' => '1000-3000万',
            'img' => 'bentley.png'
        ]];

    protected $university = [
        '加州理工学院',
        '剑桥大学',
        '牛津大学',
        '斯坦福大学',
        '麻省理工学院',
        '哈佛大学',
        '复旦大学',
        '北京大学',
        '清华大学'
    ];

    protected $storage = [
        '聪明伶俐，抱有积极向上生活态度，内心闪耀着众多美好的幻想，充满了对理想的憧憬,具有异国风情的魅力,享受着无忧无虑的曲调。',
        '情绪容易敏感，也缺乏安全感，容易对一件事情上心，做事情有坚持到底的毅力，为人重情重义，对朋友、家人都特别忠实，非常的有爱心。',
        '个性温顺亲和，平易近人，举止稳重。只是在面对已认定的问题上，则略显固执，甚至会顽强抵制。',
        '想靠自己的努力成为人上人，向往高高在上的优越感，也期待被仰慕被崇拜的感觉，有点儿自信有点儿自大，爱面子，热情阳光，对朋友讲义气。',
        '擅于察言观色，交际能力很强，朋友不少，足够真诚，但是缺点就是面对选择的时候总是犹豫不决。',
        '占有欲极强，性格属于思辨型，拥有着高度敏锐的洞察力。',
        '个性争强好胜，却能崇尚公平的竞争精神。富有冷静的判断力。',
        '崇尚自由，勇敢、果断、独立，身上有一股勇往直前的劲儿，不管有多困难，只要想，就能做。',
        '有耐心，为事最小心、为人善良,做事脚踏实地，也比较固执，不达目的是不会放手的。忍耐力也是出奇的强大，同时也非常勤奋。',
        '很聪明，最大的特点是创新，追求独一无二的生活，个人主义色彩很浓重。对人友善又注重隐私。算得上是”友谊之星“，喜欢结交每一类朋友。',
        '温柔多情，典型的风流才子范。充满了浪漫唯美的色彩，对生活如童话般的追求,知足也常乐。'
    ];

    public function draw($host)
    {

        $total = count($this->dataMap);
        $idx = rand(0, $total - 1);
        $data = $this->dataMap[$idx];

        $img = Image::canvas(400, 700, '#eeeeee');
        $jiyin = Image::make(resource_path('images/jiyin.png'));
        $jiyin->resize(400, 700);
        $img->insert($jiyin, 'left-top', 0, 0);
        $car = Image::make(resource_path('images/' . $data['img']));
        $car->resize(150, 100);
        $img->insert($car, 'top-right', 20, 20);
        $img->text('宝宝职业预测', 20, 40, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(26);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('根据您的宝宝基因预测, 您的宝宝未来成就不可限量', 20, 140, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来职业是: ', 20, 180, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text($data['occupation'], 180, 175, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来年薪是:', 20, 220, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text($data['income'], 180, 215, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来座驾是:', 20, 260, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text($data['car'], 180, 255, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $img->text('您的宝宝未来学校是:', 20, 300, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(16);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $total = count($this->university);
        $idx = rand(0, $total - 1);
        $img->text($this->university[$idx], 180, 295, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(24);
            $font->color('#0000ff');
            $font->align('left');
            $font->valign('top');
        });

        $img->text('宝宝性格特点:', 20, 380, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(18);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $line = 60;
        $total = count($this->storage);
        $idx = rand(0, $total - 1);
        $character = $this->storage[$idx];
        $length = mb_strlen($character);
        $rows = ceil(floatval($length) / floatval($line));
        for ($i = 0; $i <= $rows + 1; $i++) {
            $rowText = mb_strcut($character, $i * $line, $line);
            $img->text($rowText, 20, 420 + $i * 30, function ($font) {
                $font->file(resource_path('PingFang.ttc'));
                $font->size(14);
                $font->color('#000000');
                $font->align('left');
                $font->valign('top');
                // $font->angle(45);
            });
        }

        $qrcode = app(BaconQrCodeGenerator::class);

        $qrcode = $qrcode->format('png')->margin(1)->size(90)->generate(sprintf('http://%s/finger-site/index.html?ob=%s&token=%s', $host, base64_encode(date('YmdHis')), hash('CRC32', microtime(true))));

        $img->insert($qrcode, 'bottom-right', 20, 20);
        $img->rectangle(260, 684, 395, 696, function ($draw) {
            $draw->background('#ffffff');
        });
        $img->text('长按识别二维码开始测试', 260, 685, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(12);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });
        return $img->response('png');
    }

    public function drawDrinkTicket($host, $name)
    {
        $name = empty($name) ? '签名' : $name;

        $drinkTicket = Image::make(resource_path('images/export-bk.png'));

        $w = $drinkTicket->getWidth();
        $h = $drinkTicket->getHeight();

        $drinkTicket->text($name, $w * 0.618, $h - 220, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });
        $date = sprintf('%s年%s月%s日', Carbon::now('PRC')->year, Carbon::now('PRC')->month, Carbon::now('PRC')->day);
        $drinkTicket->text($date, $w * 0.618, $h - 160, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });


        $finger = Image::make(resource_path('images/FingerprintPic.png'));
        $finger->resize(260, 260);
        $finger->colorize(100, -100, -100);
        $finger->rotate(15);
        $drinkTicket->insert($finger, 'top-left', intval($w * 0.618 - 100), $h - 330);

        $qrcode = app(BaconQrCodeGenerator::class);

        $qrcode = $qrcode->format('png')->margin(1)->size(120)->generate(sprintf('http://%s/tickets/export.html?ob=%s&token=%s', $host, base64_encode(date('YmdHis')), hash('CRC32', microtime(true))));

        $drinkTicket->insert($qrcode, 'bottom-left', 80, 80);

        return $drinkTicket->response('png');
    }

    public function fightTicket($host, $loser, $winer)
    {
        $loser = empty($loser) ? '签名' : $loser;
        $winer = empty($winer) ? '签名' : $winer;

        $drinkTicket = Image::make(resource_path('images/fighter-bk.png'));

        $w = $drinkTicket->getWidth();
        $h = $drinkTicket->getHeight();

        $year = Carbon::now('PRC')->year;
        $month = Carbon::now('PRC')->month;
        $day = Carbon::now('PRC')->day;

        $drinkTicket->text($loser, 130, 290, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(30);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($year, 320, 290, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(30);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($month, 415, 292, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(30);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($day, 495, 292, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(30);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($winer, 240, 365, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(35);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text(rand(15, 20), 120, 500, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(35);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($loser, 420, 610, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            $font->angle(3);
        });

        $drinkTicket->text($year, 365, 665, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            $font->angle(3);
        });

        $drinkTicket->text($month, 460, 663, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            $font->angle(3);
        });

        $drinkTicket->text($day, 525, 662, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            $font->angle(3);
        });


        $qrcode = app(BaconQrCodeGenerator::class);

        $qrcode = $qrcode->format('png')->margin(1)->size(120)->backgroundColor(255, 255, 255)->generate(sprintf('http://%s/tickets/loser.html?ob=%s&token=%s', $host, base64_encode(date('YmdHis')), hash('CRC32', microtime(true))));

        $img = imagecreatefromstring($qrcode);

        imagecolortransparent($img, imagecolorallocate($img, 255, 255, 255));

        $drinkTicket->insert($qrcode, 'bottom-left', 80, 80);

        return $drinkTicket->response('png');
    }

    public function compensateTicket($host, $name)
    {
        $name = empty($name) ? '签名' : $name;

        $drinkTicket = Image::make(resource_path('images/compensate-bk.png'));

        $w = $drinkTicket->getWidth();
        $h = $drinkTicket->getHeight();

        $drinkTicket->text($name, $w * 0.618 + 100, $h - 320, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });
        $year = Carbon::now('PRC')->year;
        $month = Carbon::now('PRC')->month;
        $day = Carbon::now('PRC')->day;
        $drinkTicket->text($year, $w * 0.618 - 50, $h - 210, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($month, $w * 0.618 + 40, $h - 210, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($day, $w * 0.618 + 100, $h - 210, function ($font) {
            $font->file(resource_path('fonts/write.TTF'));
            $font->size(40);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });


        $qrcode = app(BaconQrCodeGenerator::class);

        $qrcode = $qrcode->format('png')->margin(1)->size(120)->backgroundColor(255, 255, 255)->generate(sprintf('http://%s/tickets/compensate.html?ob=%s&token=%s', $host, base64_encode(date('YmdHis')), hash('CRC32', microtime(true))));

        $img = imagecreatefromstring($qrcode);

        imagecolortransparent($img, imagecolorallocate($img, 255, 255, 255));

        $drinkTicket->insert($img, 'bottom-left', 120, 160);

        return $drinkTicket->response('png');
    }


    public function psychosisTicket($host, $name, $age, $sex)
    {
        $name = empty($name) ? '签名' : $name;
        $age = empty($age) ? '10' : $age;
        $sex = empty($sex) ? '男' : $sex;

        $drinkTicket = Image::make(resource_path('images/psychosis-bk.png'));

        $w = $drinkTicket->getWidth();
        $h = $drinkTicket->getHeight();

        $drinkTicket->text($name, 270, 480, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(25);
            $font->color('#666666');
            $font->align('left');
            $font->valign('top');
            $font->angle(1);
        });

        $drinkTicket->text($sex, 475, 480, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(25);
            $font->color('#666666');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });

        $drinkTicket->text($age, 660, 480, function ($font) {
            $font->file(resource_path('PingFang.ttc'));
            $font->size(25);
            $font->color('#666666');
            $font->align('left');
            $font->valign('top');
            // $font->angle(45);
        });


        $qrcode = app(BaconQrCodeGenerator::class);

        $qrcode = $qrcode->format('png')->margin(1)->size(120)->backgroundColor(255, 255, 255)->generate(sprintf('http://%s/tickets/psychosis.html?ob=%s&token=%s', $host, base64_encode(date('YmdHis')), hash('CRC32', microtime(true))));

        $img = imagecreatefromstring($qrcode);

        imagecolortransparent($img, imagecolorallocate($img, 255, 255, 255));

        $drinkTicket->insert($img, 'bottom-left', 200, 220);

        return $drinkTicket->response('png');
    }
}