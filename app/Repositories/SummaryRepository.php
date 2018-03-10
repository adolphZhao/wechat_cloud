<?php
namespace App\Repositories;

use App\Models\Domain;
use App\Models\WechatBindUrl;
use App\Models\WechatSettings;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class SummaryRepository
{
    public function get($id)
    {
        $domain = Domain::query()
            ->join('wechat_public_config_hosts', 'host_id', '=', 'wechat_public_config_hosts.id')
            ->where('id', $id)
            ->first([
                'wechat_public_config_hosts.hosts as domain',
                'wechat_public_domain_states.*'
            ]);
        return $domain;
    }

    public function delete($id)
    {
        $domain = Domain::query()->where('id', $id)->first();
        $hostId = $domain->host_id;
        WechatBindUrl::query()->where('id', $hostId)->delete();
        return Domain::query()->where('id', $id)->delete();
    }

    public function update($id, $attributes)
    {

        $domain = Domain::query()
            ->join('wechat_public_config_hosts', 'host_id', '=', 'wechat_public_config_hosts.id')
            ->where('wechat_public_domain_states.id', $id)
            ->first(['wechat_public_config_hosts.hosts']);

        $relation = @json_decode(\Cache::get('GUIDE_RELATION'), true);

        $cached = [];
        while (!empty($relation)) {
            $d = array_shift($relation);

            if (array_get($d, 'domain') != $domain->hosts) {
                $cached [] = $d;
            }
        }

        \Cache::put('GUIDE_RELATION', json_encode($cached), array_get($cached, '0.guide_time', 0) * 3600);

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
            ])->toArray();
        return $domains;
    }

    public function getQrCode($url)
    {
        /**
         * @var BaconQrCodeGenerator $qrcode
         */

        $qrcode = app(BaconQrCodeGenerator::class);

        return 'data:image/jpg;base64,' . base64_encode($qrcode->format('png')->size(120)->margin(1)->generate($url));
    }
}