<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventRecovery extends Model
{
    //
    // protected $connection = 'zabbix';
    protected $primaryKey = 'eventid';
    protected $table = 'event_recovery';

    public function event()
    {
        return $this->hasOne(Event::class, 'eventid', 'eventid');
    }

    public function r_event()
    {
        return $this->hasOne(Event::class, 'eventid', 'r_eventid');
    }
}
