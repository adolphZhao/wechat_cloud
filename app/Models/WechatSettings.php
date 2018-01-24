<?php
namespace App\Models;

class WechatSettings extends BaseModel
{
    protected $fillable = [
        'title',
        'app_id',
        'app_secret',
    ];

    protected $table = 'wechat_public_configs';
}