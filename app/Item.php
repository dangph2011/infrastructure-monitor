<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    public function host()
    {
        return $this->belongsTo(Host::class, 'hostid', 'hostid');
    }

    public function graphsItems()
    {
        return $this->hasMany(GraphsItem::class, 'itemid', 'itemid');
    }
}
