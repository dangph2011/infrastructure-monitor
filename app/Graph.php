<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    //
    // protected $table = 'graphs';
    protected $primaryKey = 'graphid';
    public function graphsItems()
    {
        return $this->hasMany(GraphsItem::class, 'graphid', 'graphid');
    }
}
