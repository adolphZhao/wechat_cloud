<?php
namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Video;
use App\Models\VideoHistory;
use App\Models\WechatBindUrl;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecordVideoVisitCommand extends Command
{
    protected $signature = 'record:video-visit';

    protected $httpClient;

    public function handle()
    {
        $videos = Video::all();
        foreach ($videos as $video) {
            VideoHistory::create([
                'video_id' => $video->id,
                'visit_counter' => $video->views,
                'date' => Carbon::now('PRC')->format('Y-m-d')
            ]);

            Video::query()->where('id', $video->id)->update([
                'yesterday' => $video->views
            ]);
        }
    }
}