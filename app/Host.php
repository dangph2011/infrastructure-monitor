<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    //
    // protected $table = 'hosts';

    public function hostGroups()
    {
        return $this->hasMany(HostsGroup::class);
    }
}
