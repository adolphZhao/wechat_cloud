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
        'template'
    ];

    protected $table = 'wechat_public_videos';

    public function getConfig()
    {
        $this->hasMany(VideoConfig::class, 'video_id', 'id')->getResults();
    }
}