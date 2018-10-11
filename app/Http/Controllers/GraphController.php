<?php

namespace App\Http\Controllers;

use App\Graph;
use App\Group;
use App\Host;
use App\Item;
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

        //get items based on selected graph
        $items = Graph::find($rq_graphid)->items;
        // $items = Item::whereHas('graphs', function ($query) use ($rq_graphid) {
        //     $query->where('graphs.graphid', $rq_graphid);
        // })->get();

        $data = collect();
        $layout = collect();
        if ($rq_graphid != 0) {
            $graph = Graph::find($rq_graphid);
            if ($graph->graphtype == GRAPH_TYPE_NORMAL) {
                $items->each(function ($item) use ($data, $rq_graphid) {
                    //get data
                    $clockValue = $this->getClockAndValueNumericData($item->itemid, $item->value_type);
                    //get delay time to handle gaps data
                    $delayTime = Item::convertToTimestamp($item->delay);
                    //add null to gaps data
                    smoothClockData($clockValue, $delayTime);

                    $data->push(createDataLine($clockValue[0], $clockValue[1], "line", $item->name, false, 1.5));
                });
                //Draw line graph

                $rangeslider = collect();
                $rangeselector = collect(['buttons' => getSelectorOption(['1m', '3m', '6m', 'ytd', '1y'])]);
                $layout = createLayoutLine(
                    createXAxisLayoutLine('date', 'Date', true, $rangeselector, $rangeslider),
                    createYAxisLayoutLine(null, 'Value', true),
                    $graph->name
                );
                // return view('graphs.view', compact('groups', 'hosts', 'rq_groupid', 'graphs', 'rq_hostid', 'data', 'layout', 'rq_graphid'));

            } elseif ($graph->graphtype == GRAPH_TYPE_STACKED) {
                //Draw stacked (area chart)
                $items->each(function ($item) use ($data, $rq_graphid) {
                    //get data
                    $clockValue = $this->getClockAndValueNumericData($item->itemid, $item->value_type, 'trends');
                    //get delay time to handle gaps data
                    $delayTime = Item::convertToTimestamp($item->delay);
                    //add null to gaps data
                    smoothClockData($clockValue, $delayTime);
                    //create data stacked
                    $data->push(createDataStacked($clockValue[0], $clockValue[1], "one", "percent"));
                });
                //Draw line graph
                $rangeslider = collect();
                $rangeselector = collect(['buttons' => getSelectorOption(['1m', '3m', '6m', 'ytd', '1y'])]);
                $layout = createLayoutLine(
                    createXAxisLayoutLine('date', 'Date', true, $rangeselector, $rangeslider),
                    createYAxisLayoutLine(null, 'Value', true),
                    $graph->name
                );
            } elseif ($graph->graphtype == GRAPH_TYPE_PIE) {
                //Draw pie graph
                //Get total of pie
                $value = collect();
                $label = collect();
                $items->each(function ($item) use ($value, $label) {
                    $clockValue = $this->getClockAndValueNumericData($item->itemid, $item->value_type);
                    if ($item->pivot->type == 2) {
                        $value->prepend($clockValue[1]->avg());
                        $label->prepend($item->name);
                    } else {
                        $value->push($clockValue[1]->avg());
                        $label->push($item->name);
                    }
                    //get delay time to handle gaps data
                });
                $value[0] -= $value->slice(1)->sum();

                $data->push(createDataPie($value, $label));

                $layout = createLayoutTitle($graph->name);

            } elseif ($graph->graphtype == GRAPH_TYPE_EXPLODED) {
                //Draw exploded graph
            }
        }
        // dd($tracers);
        // return $layout;
        return view('graphs.view', compact('groups', 'hosts', 'rq_groupid', 'graphs', 'rq_hostid', 'data', 'layout', 'rq_graphid'));
    }

    // max clock 2147483647 03:14:07 UTC on 19 January 2038 like Y2K
    public function getClockAndValueNumericData($itemid, $data_type, $table='history', $min_clock = 0, $max_clock = 2147483647)
    {
        // $table = 'history';
        if ($data_type == ITEM_VALUE_TYPE_UNSIGNED) {
            $table .= '_uint';
        }

        $tableData = DB::connection('zabbix')->table($table)->where('itemid', $itemid)->where('clock', ">=", $min_clock)
            ->where('clock', "<", $max_clock)->orderBy('clock')->get();
        $xData = collect();
        $yData = collect();
        if (starts_with($table, 'history')) {
            $tableData->each(function ($history) use ($xData, $yData) {
                //multiple 1000,  miliseconds in JS
                $xData->push($history->clock * 1000);
                $yData->push($history->value);
            });
        } else if (starts_with($table, 'trends')) {
            $tableData->each(function ($history) use ($xData, $yData) {
                //multiple 1000,  miliseconds in JS
                $xData->push($history->clock * 1000);
                $yData->push($history->value_avg);
            });
        }
        return collect([$xData, $yData]);
    }

    //download graph
    public function download($data, $layout)
    {
        $pdf = \PDF::loadView('graphs.plotly_download', compact('data', 'layout'))->setPaper('a4')->setOrientation('landscape');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 10000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        return $pdf->download('google.pdf');
        // return view('googlechart');
    }
}
