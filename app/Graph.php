<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    //
    public function graphsItems()
    {
        return $this->hasMany(GraphsItem::class, 'graphid', 'graphid');
    }
}
