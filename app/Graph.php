<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    //
    // protected $table = 'graphs';
    protected $primaryKey = 'graphid';
    protected $connection = 'zabbix';
    public function graphsItems()
    {
        return $this->hasMany(GraphsItem::class, 'graphid', 'graphid');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'graphs_items', 'graphid', 'itemid')->withPivot('type');
    }

    public static function getGraphByGroupAndHost($groupids, $hostids)
    {
        //get graphs based on selected group and host
        $graphs = Graph::whereIn('flags', [0, 4])
            ->whereHas('items', function ($query) use ($hostids, $groupids) {
                $query->whereHas('host', function ($query) use ($hostids, $groupids) {
                    $query->where('status', 0)->whereIn('hosts.hostid', $hostids)
                        ->whereHas('groups', function ($query) use ($groupids) {
                            $query->whereIn('groups.groupid', $groupids);
                        });
                });
            })->orderBy('name')->get();
        return $graphs;
    }
}
