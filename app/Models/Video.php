<?php
namespace App\Models;

class Video extends BaseModel
{
    protected $fillable = [
        'id',
        'code',
        'stop_time'
    ];

    protected $hidden=['created_at','updated_at'];

    protected $table = 'wechat_public_videos';

    public function getConfig()
    {
        $this->hasMany(VideoConfig::class, 'video_id', 'id')->getResults();
    }
}