<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    //
    // protected $table = 'hosts';
    protected $primaryKey = 'hostid';
    protected $connection = 'zabbix';

    public function hostGroups()
    {
        return $this->hasMany(HostsGroup::class, 'hostid', 'hostid');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'hostid', 'hostid');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'hosts_groups', 'hostid', 'groupid');
    }

    public static function getHostByGroupIds($groupids) {
        //get hosts based on selected group
        $hosts = Host::where('status', 0)->WhereIn('flags', [0, 1])
        ->whereHas('groups', function ($query) use ($groupids) {
            $query->whereIn('hstgrp.groupid', $groupids);
        })
        ->whereHas('items', function ($query) {
            $query->whereHas('graphs', function ($query) {
                $query->whereIN('flags', [0, 4]);
            });
        })->get();
        return $hosts;
    }

    public static function getAllHost() {
        $hosts = Host::where('status', 0)->WhereIn('flags', [0, 1])
        ->whereHas('items', function ($query) {
            $query->whereHas('graphs', function ($query) {
                $query->whereIN('flags', [0, 4]);
            });
        })->get();
        return $hosts;
    }
}
