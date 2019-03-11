<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    //
    // protected $connection = 'zabbix';
    protected $primaryKey = 'triggerid';
    public function items()
    {
        return $this->belongsToMany(Item::class, 'functions', 'triggerid', 'itemid');
    }

    public function getHostByTriggerId($triggerId) {
        return \App\Host::whereHas('items', function($query) use ($triggerId) {
            $query->whereHas('triggers', function($query) use ($triggerId){
                $query->where('triggers.triggerid', $triggerId);
            });
        })->get();
    }
}
