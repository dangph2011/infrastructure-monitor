<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GraphsItem extends Model
{
    //

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function graph()
    {
        return $this->belongsTo(Graph::class);
    }
}
