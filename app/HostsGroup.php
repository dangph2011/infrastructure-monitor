<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostsGroup extends Model
{
    //
    // protected $table = 'hosts_groups';
    protected $primaryKey = 'hostgroupid';
    // protected $connection = 'zabbix';

    public function host()
    {
        return $this->belongsTo(Host::class, 'hostid', 'hostid');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'groupid','groupid');
    }
}
