<?php
namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Video;
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
            \Cache::put('DOMAIN_HITS_' . md5($host), 0, 3600 * 24);
            $this->info(sprintf('sync hits from cache : domain =>%s, %d', $host, $hits));
        }

        $videos = Video::all();
        foreach ($videos as $video) {
            $cacheKey = 'VIDEO_HITS_' . $video->map_id;
            $hits = \Cache::get($cacheKey);
            Video::incView($video->map_id, $hits);
            \Cache::put('VIDEO_HITS_' . $video->map_id, 0, 3600 * 24);
            $this->info(sprintf('sync hits from cache : video =>%d ,views=> %d', $video->map_id, $hits));
        }
    }
}