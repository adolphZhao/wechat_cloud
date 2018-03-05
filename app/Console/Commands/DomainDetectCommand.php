<?php
namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\WechatBindUrl;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;

class DomainDetectCommand extends Command
{
    protected $signature = 'detect:domain';

    protected $httpClient;

    protected $appId = 'wx87a7fd4357bd3d80';

    protected $appSecret = '3b858cb7408e701c7c576239ab59f775';

    protected $accessToken;


    public function __construct(Client $client)
    {
        $this->httpClient = $client;
        parent::__construct();
    }

    public function handle()
    {
        $bindUrls = WechatBindUrl::query()->get();
        $retPool = [];
        $retPool['domain'] = [];

        if (empty($bindUrls->count())) {
            $this->warn("没有检测到需要测试的域名......\n");
            exit;
        }

        foreach ($bindUrls as $domain) {

            $this->info('检测Domain => ' . $domain->hosts . "\n");

            $status = $this->detectDomainStatus($domain->hosts);

            $ipAddress = gethostbyname($domain->hosts);
            if ($ipAddress) {
                Domain::query()->where('host_id', $domain->id)->update(['ip_address' => $ipAddress]);
            }

            if ($status->status == 0 || $status->status == 3) {
                $this->info($status->errmsg . '  =>  ' . $domain->hosts . "\n");
                $this->flagDomainFromPool($domain, 0);
            } else {
                $this->warn($status->errmsg . '  =>  ' . $domain->hosts . '.' . $status->status . "\n");
                $this->flagDomainFromPool($domain, 1);
            }
            sleep(2);
        }
    }

    function detectDomainStatus($domain)
    {
        $response = $this->httpClient->get(sprintf('http://vip.weixin139.com/weixin/ll0358a.php?domain=%s', $domain));
        $status = $response->getBody()->getContents();

        return @json_decode($status);
    }

    function flagDomainFromPool($domain, $status)
    {
        if ($status) {
            $status = Domain::incState($domain->id);
            if ($status > 0 && $status < 3) {
                $this->accessToken();
                $this->sendMessage($domain->hosts);
            }
        } else {
            Domain::flushState($domain->id);
        }
    }

    protected function accessToken()
    {
        if (!file_exists(storage_path('access_token'))) {
            $this->accessToken = $this->getTokenByHttp();
        } else {
            $tokenObj = @json_decode(file_get_contents(storage_path('access_token')), true);
            if (Carbon::now()->getTimestamp() - array_get($tokenObj, 'updated_at', 0) > 7200) {
                $this->accessToken = $this->getTokenByHttp();
            } else {
                $this->accessToken = array_get($tokenObj, 'access_token', '');
            }
        }
        return $this->accessToken;
    }

    protected function getTokenByHttp()
    {
        $response = $this->httpClient->get(sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $this->appId, $this->appSecret));
        $content = $response->getBody()->getContents();
        $tokenObj = @json_decode($content, true);
        array_set($tokenObj, 'updated_at', Carbon::now()->getTimestamp());
        file_put_contents(storage_path('access_token'), json_encode($tokenObj));
        return array_get($tokenObj, 'access_token', '');
    }

    protected function templates()
    {
        $response = $this->httpClient->get(sprintf('https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=%s', $this->accessToken));
        $content = $response->getBody()->getContents();
        $this->info($content);
    }

    protected function sendMessage($domain)
    {
        $response = $this->httpClient->post(sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s', $this->accessToken),
            [
                RequestOptions::JSON => [
                    'touser' => 'oWTePwSS730fPZw6L_oCTu0j-cxo',
                    'template_id' => 'OUIvBG3Ar0mUl1g5BB2O0lNBXrWHeCKMpVoGtlMCA7g',
                    'url' => 'https://www.baidu.com',
                    'data' => [
                        'domain' => [
                            'value' => $domain,
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