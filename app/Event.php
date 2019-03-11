<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    // protected $connection = 'zabbix';
    protected $primaryKey = 'eventid';

    public function problem()
    {
        return $this->hasOne(Problem::class, 'eventid');
    }

    public function trigger()
    {
        return $this->hasOne(Trigger::class, 'triggerid', 'objectid');
    }

    public function eventRecovery()
    {
        return $this->hasOne(EventRecovery::class, 'eventid', 'eventid');
    }
}
