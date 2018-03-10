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
        if (file_exists(storage_path('access_token'))) {
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
            case 'chat':
                $this->chatAndroid();
                break;
            case 'menu':
                $this->menus();
                break;
            case 'guide':
                $this->stopGuide();
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
                    'url' => 'https://www.baidu.com',
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

    protected function chatAndroid()
    {
        $response = $this->httpClient->post('http://openapi.tuling123.com/openapi/api/v2', [
            RequestOptions::JSON => [
                "reqType" => 0,
                "perception" => [
                    "inputText" => [
                        "text" => "附近的酒店"
                    ]

                    ,
                    "inputImage" => [
                        "url" => "imageUrl"
                    ],
                    "selfInfo" => [
//                        "location" => [
//                            "city" => "北京",
//                            "province" => "北京",
//                            "street" => "信息路"
//                        ]
                    ]
                ],
                "userInfo" => [
                    "apiKey" => "ebaa90dea958421384a9dc8c4bd32760",
                    "userId" => "228065"
                ]
            ]]);
        $results = @json_decode($response->getBody()->getContents(), true);
        $text = '';
        foreach (array_get($results, 'results') as $result) {
            if (array_get($result, 'resultType') == 'text') {
                $text .= ' ' . array_get($result, 'values.text');
            } elseif (array_get($result, 'resultType') == 'url') {
                $text .= ' ' . array_get($result, 'values.url');
            }
        }

        $this->info($text);
    }

    protected function menus()
    {

        $response = $this->httpClient->post(sprintf('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s', $this->accessToken), [
                RequestOptions::JSON => [
                    "button" => [
                        [
                            "type" => "click",
                            "name" => "Stat",
                            "key" => "V1003_01_STAT_USERS"
                        ],
                        [
                            "name" => "Guide",
                            "sub_button" => [
                                [
                                    "type" => "click",
                                    "name" => "Start",
                                    "key" => "V1003_START_GUIDE"
                                ],
                                [
                                    "type" => "click",
                                    "name" => "Stop",
                                    "key" => "V1004_STOP_GUIDE"
                                ],
                                [
                                    "type" => "click",
                                    "name" => "Hosts",
                                    "key" => "V1005_HOST_LIST"
                                ]
                            ]
                        ],
                        [
                            "name" => "Server",
                            "sub_button" => [
                                [
                                    "type" => "click",
                                    "name" => "Start",
                                    "key" => "V1001_START_SERVER"
                                ],
                                [
                                    "type" => "click",
                                    "name" => "Stop",
                                    "key" => "V1002_STOP_SERVER"
                                ]
                            ]
                        ]
                    ]
                ]]
        );

        $this->info($response->getBody()->getContents());

    }

    protected function stopGuide()
    {
        \Cache::put('STOP_GUIDE', 'STOP', 3600 * 24);

        return 'STOP';
    }
}