<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
    // protected $table = 'graphs';
    protected $primaryKey = 'reportid';
    protected $connection = 'zabbix';
    public function reportGraphs()
    {
        return $this->hasMany(ReportsGraph::class, 'graphid', 'graphid');
    }

    public function graphs()
    {
        return $this->belongsToMany(Graph::class, 'reports_graphs', 'reportid', 'graphid');
    }

    public function saveReport($graphIDs) {
        $this->graphs()->sync($graphIDs);
    }

    public function destroyReport() {
        $this->graphs()->detach();
    }
}
