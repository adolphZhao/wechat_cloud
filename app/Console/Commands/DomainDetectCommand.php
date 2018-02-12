<?php
namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\WechatBindUrl;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class DomainDetectCommand extends Command
{
    protected $signature = 'detect:domain';

    protected $httpClient;

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
            sleep(3);
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
            Domain::incState($domain->id);
        } else {
            Domain::flushState($domain->id);
        }
    }
}