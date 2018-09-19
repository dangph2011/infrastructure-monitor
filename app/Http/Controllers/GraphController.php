<?php

namespace App\Http\Controllers;

use App\Graph;
use App\Group;
use App\Host;
use App\Item;
use App\History;
use App\Trend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $layout = collect();

        $items->each(function ($item) use ($data, $rq_graphid, &$layout) {
            //get data
            $clock_value = $this->getClockAndValueNumericData($item->itemid, $item->value_type);
            //get delay time to handle gaps data
            $delay_time = Item::convertToTimestamp($item->delay);
            $timestamp = 0;
            //add null to missing data
            foreach ($clock_value[0] as $key => $clock) {
                if ($key != 0) {
                    if ($clock_value[0][$key] - $timestamp > (2*$delay_time)) {
                        $clock_value[0]->splice($key, 0, $timestamp + $delay_time);
                        $clock_value[1]->splice($key, 0, "null");
                        // $key++;
                    }
                }
                $timestamp = $clock_value[0][$key];
            }

            //Draw graph based on graph type
            $graph = Graph::find($rq_graphid);
            if ($graph->graphtype == GRAPH_TYPE_NORMAL) {
                //Draw line graph
                $data->push(createDataLine($clock_value[0], $clock_value[1], "line", $item->name, false, 1.5));
                $rangeslider = collect();
                $rangeselector = collect(['buttons' => getSelectorOption(['1m','3m', '6m', 'ytd', '1y'])]);
                $layout = createLayoutLine(
                    createXAxisLayoutLine('date', 'Date', true, $rangeselector, $rangeslider),
                    createYAxisLayoutLine(null,'Value', true),
                    'Line Graph'
                );

            } elseif ($graph->graphtype == GRAPH_TYPE_STACKED) {
                //Draw stacked (area chart)
                //calculate average of data

            } elseif ($graph->graphtype == GRAPH_TYPE_PIE) {
                //Draw pie graph

            } elseif ($graph->graphtype == GRAPH_TYPE_EXPLODED) {
                //Draw exploded graph
            }
        });
        // dd($tracers);
        // return $layout;
        return view('graphs.view', compact('groups', 'hosts', 'rq_groupid', 'graphs', 'rq_hostid', 'data', 'layout', 'rq_graphid'));
    }



    // max clock 2147483647 03:14:07 UTC on 19 January 2038 like Y2K
    public function getClockAndValueNumericData($itemid, $data_type, $min_clock = 0, $max_clock=2147483647)
    {
        $table = 'history';
        if ($data_type == ITEM_VALUE_TYPE_UNSIGNED) {
            $table .= '_uint';
        }
        $histories = DB::table($table)->where('itemid', $itemid)->where('clock', ">=", $min_clock)
                        ->where('clock', "<", $max_clock)->orderBy('clock')->get();
        $x_data = collect();
        $y_data = collect();
        $histories->each(function ($history) use ($x_data, $y_data) {
            //multiple 1000,  miliseconds in JS
            $x_data->push($history->clock*1000);
            $y_data->push($history->value);
        });
        return collect([$x_data, $y_data]);
    }
}
