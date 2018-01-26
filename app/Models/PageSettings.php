<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/26
 * Time: 上午11:18
 */

namespace App\Models;


class PageSettings extends BaseModel
{
    protected $fillable = [
        'video_id',
        'share_times',
        'ad_top_show',
        'ad_bottom_show',
        'ad_back_show',
        'ad_author_show',
        'ad_original_show',
        'description'
    ];

    protected $table = 'page_interface_settings';

}