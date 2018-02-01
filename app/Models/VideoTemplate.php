<?php
namespace App\Models;

class VideoTemplate extends BaseModel
{
    protected $fillable = [
        'id',
        'prefix',
        'core',
        'suffix',
        'template',
        'video_id',
        'video_code'
    ];

    protected $table = 'video_title_template';
}