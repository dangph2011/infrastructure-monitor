<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $primaryKey = 'itemid';
    // protected $connection = 'zabbix';
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
        return $this->belongsToMany(Graph::class, 'graphs_items', 'itemid', 'graphid')->withPivot('type', 'drawtype', 'color', 'sortorder');
    }

    public function triggers()
    {
        return $this->belongsToMany(Trigger::class, 'functions', 'itemid', 'triggerid')->wherePivotIn('name', ['last','min','avg','max']);
    }

    //convert delay time to unix time gaps, interval time to get data
    public static function convertToTimestamp($time){
        if(strpos($time,'s')){
            $time=str_replace("s","",$time);
        }elseif (strpos($time,'m')){
            $time=str_replace("m","",$time)*60;
        }elseif (strpos($time,'h')){
            $time=str_replace("h","",$time)*3600;
        }elseif (strpos($time,'d')){
            $time=str_replace("d","",$time)*86400;
        }elseif (strpos($time,'w')){
            $time=str_replace("w","",$time)*604800;
        }
        return $time*1000;
    }
}
