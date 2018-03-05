<?php
namespace App\Console\Commands;


use Carbon\Carbon;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class WXTestCommand extends Command
{
    protected $httpClient;

    protected $signature = 'wx:invoke {api}';

    protected $appId = 'wx87a7fd4357bd3d80';

    protected $appSecret = '3b858cb7408e701c7c576239ab59f775';

    protected $accessToken;

    public function __construct(Client $client)
    {
        if(file_exists(storage_path('access_token'))){
            $this->accessToken = file_get_contents(storage_path('access_token'));
        }

        $this->httpClient = $client;
        parent::__construct();
    }


    public function handle()
    {
        //  {"signature":"741c688336e9932256143b08b10c796b0de1f5e4","echostr":"3565257654458645983","timestamp":"1519908319","nonce":"102372397"}
        $api = $this->argument('api');

        switch ($api) {
            case 'token':
                $this->token();
                break;
            case 'accessToken' :
                $this->accessToken();
                break;
            case 'templates':
                $this->templates();
                break;
            case 'message':
                $this->sendMessage();
                break;
        }
    }

    protected function token()
    {
        $signature = '741c688336e9932256143b08b10c796b0de1f5e4';
        $timestamp = '1519908319';
        $echostr = '3565257654458645983';
        $nonce = '102372397';
        $token = 'c4ca4238a0b923820d';

        $list = [
            $token,
            $timestamp,
            $nonce
        ];
        sort($list);
        $signatureSHA1 = sha1(implode($list));
        if ($signature == $signatureSHA1) {
            $this->info($echostr);
        } else {
            $this->error('signature failure.');
        }
    }

    protected function accessToken()
    {
        $response = $this->httpClient->get(sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $this->appId, $this->appSecret));
        $content = $response->getBody()->getContents();
        $token = array_get(@json_decode($content, true), 'access_token', '');
        file_put_contents(storage_path('access_token'), $token);
        $this->info($token);
    }

    protected function templates()
    {
        $response = $this->httpClient->get(sprintf('https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=%s', $this->accessToken));
        $content = $response->getBody()->getContents();
        $this->info($content);
    }

    protected function sendMessage()
    {
        $response = $this->httpClient->post(sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s', $this->accessToken),
            [
                RequestOptions::JSON => [
                    'touser' => 'oWTePwSS730fPZw6L_oCTu0j-cxo',
                    'template_id' => 'OUIvBG3Ar0mUl1g5BB2O0lNBXrWHeCKMpVoGtlMCA7g',
                    'url'=>'https://www.baidu.com',
                    'data' => [
                        'domain' => [
                            'value' => 'www.baidu.com',
                            "color" => "#ff9900"
                        ],
                        'time' => [
                            'value' => Carbon::now('PRC')->format('Y-m-d H:i:s'),
                            "color" => "#ff9900"
                        ]
                    ]
                ]
            ]
        );

        $this->info($response->getBody()->getContents());
    }
}