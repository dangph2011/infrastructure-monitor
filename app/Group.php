<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    // protected $table = 'groups';
    protected $primaryKey = 'groupid';

    public function hostsGroups()
    {
        return $this->hasMany(HostsGroup::class, 'groupid', 'groupid');
    }
}
