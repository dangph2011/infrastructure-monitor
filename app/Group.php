<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    // protected $table = 'groups';
    protected $table = 'hstgrp';
    protected $primaryKey = 'groupid';
    // protected $connection = 'zabbix';

    public function hostsGroups()
    {
        return $this->hasMany(HostsGroup::class, 'groupid', 'groupid');
    }

    public function hosts()
    {
        return $this->belongsToMany(Host::class, 'hosts_groups', 'groupid', 'hostid');
    }

    public function getGroup() {
        $groups = $this::whereHas('hosts', function ($query) {
            $query->where('status', 0)->whereHas('items', function ($query) {
                $query->whereIN('flags', [0, 4])->whereHas('graphs', function ($query) {
                    $query->whereIN('flags', [0, 4]);
                });
            });
        })->get();
        return $groups;
    }
}
