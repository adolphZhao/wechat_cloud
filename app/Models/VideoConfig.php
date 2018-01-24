<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/17
 * Time: 下午6:43
 */

namespace App\Models;

class VideoConfig extends BaseModel
{
    protected $fillable = [
        'video_id',
        'title',
        'image'
    ];

    protected $table = 'wechat_public_video_configs';
}