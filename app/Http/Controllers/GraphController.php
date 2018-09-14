<?php

namespace App\Http\Controllers;

use App\Graph;
use App\Group;
use App\Host;
use App\Item;
use Illuminate\Http\Request;
use App\History;
use App\Trend;

class GraphController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view()
    {
        $groupid = collect();
        $hostid = collect();
        // $graphid = 0;

        $groups = Group::whereHas('hosts', function ($query) {
            $query->where('status', 0)->whereHas('items', function ($query) {
                $query->whereIN('flags', [0, 4])->whereHas('graphs', function ($query) {
                    $query->whereIN('flags', [0, 4]);
                });
            });
        })->get();

        //Get groupId request
        $rq_groupid = request('groupid', 0);
        $rq_hostid = request('hostid', 0);
        $rq_graphid = request('graphid', 0);

        if ($rq_groupid == 0) {
            $groups->each(function ($group) use ($groupid) {
                $groupid->push($group->groupid);
            });
        } else {
            $groupid->push($rq_groupid);
        }

        //get hosts based on selected group
        $hosts = Host::where('status', 0)->WhereIn('flags', [0, 1])
            ->whereHas('groups', function ($query) use ($groupid) {
                $query->whereIn('groups.groupid', $groupid);
            })
            ->whereHas('items', function ($query) {
                $query->whereHas('graphs', function ($query) {
                    $query->whereIN('flags', [0, 4]);
                });
            })->get();

        if ($rq_hostid == 0) {
            $hosts->each(function ($host) use ($hostid) {
                $hostid->push($host->hostid);
            });
        } else {
            $hostid->push($rq_hostid);
        }

        //get graphs based on selected group and host
        $graphs = Graph::whereIn('flags', [0, 4])
            ->whereHas('items', function ($query) use ($hostid, $groupid) {
                $query->whereHas('host', function ($query) use ($hostid, $groupid) {
                    $query->where('status', 0)->whereIn('hosts.hostid', $hostid)
                        ->whereHas('groups', function ($query) use ($groupid) {
                            $query->whereIn('groups.groupid', $groupid);
                        });
                });
            })->orderBy('name')->get();

        // $graphid = $rq_graphid;

        //get items based on selected graph
        $items = Item::whereHas('graphs', function ($query) use ($rq_graphid) {
            $query->where('graphs.graphid', $rq_graphid);
        })->get();

        $data = collect();
        $tracers = collect();

        $items->each(function ($item) use ($tracers) {
            //get data
            $clock_value = History::getClockAndValueData($item->itemid);
            //get delay time to handle gaps data
            $delay_time = Item::convertToTimestamp($item->delay);
            $timestamp = 0;
            //add null to missing data
            foreach ($clock_value[0] as $key => $clock) {
                if ($key != 0) {
                    if ($clock_value[0][$key] - $timestamp > (2*$delay_time)) {
                        $clock_value[0]->splice($key, 0, $timestamp + $delay_time);
                        $clock_value[1]->splice($key, 0, "null");
                        $key++;
                    }
                }
                $timestamp = $clock_value[0][$key];
            }

            $tracers->push($this->createTraceLine($clock_value[0], $clock_value[1], "line", $item->name, false, 1.5));
        });

        $layout = $this->createLayoutLine(
            $this->createAxisLayout('date', 'Date'),
            $this->createAxisLayout(null,'Value'),
            'Line Graph'
        );

        // return $layout;
        return view('graphs.view', compact('groups', 'hosts', 'rq_groupid', 'graphs', 'rq_hostid', 'tracers', 'layout', 'rq_graphid'));
    }

    public function createTraceLine($x_data, $y_data, $mode, $name=null, $connectgaps=true, $size = null)
    {
        return collect([
            "x"=>$x_data,
            "y"=>$y_data,
            "mode" => $mode,
            "name" => $name,
            "connectgaps" => $connectgaps,
            "line" => ["width" => $size],
            // "type" => 'scatter',
        ]);
    }

    public function createAxisLayout($type = null, $title = null)
    {
        return collect([
            "type" => $type,
            "title" => $title,
        ]);
    }

    public function createLayoutLine($xaxis = null, $yaxis = null, $title = null)
    {
        return collect([
            "title" => $title,
            "xaxis" => $xaxis,
            "yaxis" => $yaxis,
        ]);
    }
}
