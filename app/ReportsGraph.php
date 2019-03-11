<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportsGraph extends Model
{
    protected $primaryKey = 'rgraphid';
    // protected $connection = 'zabbix';
    //
    public function report()
    {
        return $this->belongsTo(Report::class, 'reportid', 'reportid');
    }

    public function graph()
    {
        return $this->belongsTo(Graph::class, 'graphid', 'graphid');
    }
}
