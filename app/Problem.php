<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    //
    protected $connection = 'zabbix';
    protected $table = 'problem';

    public function event()
    {
        return $this->hasOne(Event::class, 'eventid');
    }

    public function trigger()
    {
        return $this->hasOne(Trigger::class, 'triggerid', 'objectid');
    }
}
