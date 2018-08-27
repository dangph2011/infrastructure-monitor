<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    //
    public function graphItems()
    {
        return $this->hasMany(GraphItem::class);
    }
}
