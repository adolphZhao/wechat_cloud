<?php
namespace App\Repositories;

use App\Models\Domain;
use App\Models\WechatBindUrl;
use App\Models\WechatSettings;

class SummaryRepository
{
    public function get($id)
    {
        $domain = Domain::query()
            ->join('wechat_public_config_hosts', 'host_id', '=', 'wechat_public_config_hosts.id')
            ->where('id',$id)
            ->first([
                'wechat_public_config_hosts.hosts as domain',
                'wechat_public_domain_states.*'
            ]);
        return $domain;
    }

    public function delete($id)
    {
        return Domain::query()->where('id', $id)->delete();
    }

    public function update($id, $attributes)
    {
        return Domain::query()->where('id', $id)->update($attributes);
    }

    public function create($attributes)
    {
        $domain = Domain::create($attributes);

        return $domain;
    }

    public function all()
    {
        $domains = Domain::query()
            ->join('wechat_public_config_hosts', 'host_id', '=', 'wechat_public_config_hosts.id')
            ->get([
                'wechat_public_config_hosts.hosts as domain',
                'wechat_public_domain_states.*'
            ]);
        return $domains;
    }
}