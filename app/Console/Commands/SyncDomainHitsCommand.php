<?php
namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\WechatBindUrl;
use Illuminate\Console\Command;

class SyncDomainHitsCommand extends Command
{
    protected $signature = 'sync:hits';

    protected $httpClient;

    public function handle()
    {
        $bindUrls = WechatBindUrl::query()->get();

        foreach ($bindUrls as $domain) {
            $host = $domain->hosts;

            $cacheKey = 'DOMAIN_HITS_' . md5($host);
            $hits = \Cache::get($cacheKey);
            Domain::incHits($domain->id, $hits);
            \Cache::put($cacheKey, 0);
            $this->info(sprintf('sync hits from cache : domain =>%s, %d', $host, $hits));
        }
    }
}