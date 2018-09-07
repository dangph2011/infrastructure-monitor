<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $primaryKey = 'itemid';
    public function host()
    {
        return $this->belongsTo(Host::class, 'hostid', 'hostid');
    }

    public function graphsItems()
    {
        return $this->hasMany(GraphsItem::class, 'itemid', 'itemid');
    }

    public function graphs()
    {
        return $this->belongsToMany(Graph::class, 'graphs_items', 'itemid', 'graphid');
    }
}
