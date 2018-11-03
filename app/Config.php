<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';
    protected $connection = 'zabbix';
    protected $primaryKey = 'configid';
}
