<?php
namespace App\Models;

class Video extends BaseModel
{
    protected $fillable = [
        'id',
        'code',
        'stop_time',
        'weight'
    ];

    protected $table = 'wechat_public_videos';

    public function getConfig()
    {
        $this->hasMany(VideoConfig::class, 'video_id', 'id')->getResults();
    }
}