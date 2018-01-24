<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/24
 * Time: 下午5:05
 */

namespace App\Models;


class Domain extends BaseModel
{
    protected $fillable = [
        'host_id',
        'status',
        'hits'
    ];

    protected $table = 'wechat_public_domain_states';

    public static function incHits($pid)
    {
        $builder = Domain::query()->where('host_id', $pid);
        if (!$builder->exists()) {
            Domain::create([
                'host_id' => $pid,
                'hits' => 0
            ]);
        } else {
            $domain = $builder->first();
            $domain->hits += 1;
            $domain->save();
        }
    }

    public static function decHits($pid)
    {
        $builder = Domain::query()->where('host_id', $pid);
        if (!$builder->exists()) {
            Domain::create([
                'host_id' => $pid,
                'hits' => 0
            ]);
        } else {
            $domain = $builder->first();
            $domain->hits -= 1;
            $domain->save();
        }
    }

    public static function incState($pid)
    {
        $builder = Domain::query()->where('host_id', $pid);
        if (!$builder->exists()) {
            Domain::create([
                'host_id' => $pid,
                'status' => 1
            ]);
        } else {
            $domain = $builder->first();
            $domain->status += 1;
            $domain->save();
        }
    }

    public static function decState($pid)
    {
        $builder = Domain::query()->where('host_id', $pid);
        if (!$builder->exists()) {
            Domain::create([
                'host_id' => $pid,
                'status' => 1
            ]);
        } else {
            $domain = $builder->first();
            $domain->status -= 1;
            $domain->save();
        }
    }

    public static function flushState($pid)
    {
        $builder = Domain::query()->where('host_id', $pid);
        if (!$builder->exists()) {
            Domain::create([
                'host_id' => $pid,
                'status' => 0
            ]);
        } else {
            $domain = $builder->first();
            $domain->status = 0;
            $domain->save();
        }
    }
}