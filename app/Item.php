<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    public function host()
    {
        return $this->belongsTo(Host::class);
    }
    public function graphItems()
    {
        return $this->hasMany(GraphsItem::class);
    }
}
