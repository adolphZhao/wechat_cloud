<?php
namespace App\Models;

class Settings extends BaseModel
{
    protected $fillable = [
        'title',
        'image',
        'url',
        'position',
    ];

    protected $table = 'wechat_public_ads_configs';
}