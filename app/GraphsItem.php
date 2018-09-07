<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GraphsItem extends Model
{
    protected $primaryKey = 'gitemid';

    //
    public function item()
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemid');
    }

    public function graph()
    {
        return $this->belongsTo(Graph::class, 'graphid', 'graphid');
    }
}
