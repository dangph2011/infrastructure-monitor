<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    // protected $table = 'groups';
    protected $primaryKey = 'groupid';
    protected $connection = 'zabbix';
    public function hostsGroups()
    {
        return $this->hasMany(HostsGroup::class, 'groupid', 'groupid');
    }

    public function hosts()
    {
        return $this->belongsToMany(Host::class, 'hosts_groups', 'groupid', 'hostid');
    }
}
