<?php
namespace App\Models;

class Video extends BaseModel
{
    protected $fillable = [
        'id',
        'map_id',
        'code',
        'stop_time',
        'weight',
        'template',
        'views'
    ];

    protected $table = 'wechat_public_videos';

    public function getConfig()
    {
        $this->hasMany(VideoConfig::class, 'video_id', 'id')->getResults();
    }

    public static function incView($vid, $views = 1)
    {
        $video = Video::query()->where('map_id', $vid)->first();
        $video->views = empty($video->views) ? $views : $video->views + $views;
        $video->save();
    }
}