<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    //
    // protected $table = 'graphs';
    protected $primaryKey = 'graphid';
    protected $connection = 'zabbix';
    public function graphsItems()
    {
        return $this->hasMany(GraphsItem::class, 'graphid', 'graphid');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'graphs_items', 'graphid', 'itemid');
    }
}
