<?php
namespace App\Models;

class VideoHistory extends BaseModel
{
    protected $fillable = [
        'id',
        'video_id',
        'visit_counter',
        'date',
    ];

    protected $table = 'video_visit_history';

}