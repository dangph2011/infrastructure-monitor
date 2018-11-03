<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $connection = 'zabbix';
    protected $primaryKey = 'eventid';

    public function problem()
    {
        return $this->hasOne(Problem::class, 'eventid');
    }
}
