<?php
namespace App\Models;

class WechatBindUrl extends BaseModel
{
    protected $fillable = [
        'hosts',
        'deleted',
    ];

    protected $table = 'wechat_public_config_hosts';
}