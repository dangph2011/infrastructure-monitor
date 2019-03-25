<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\Host;
use App\Graph;
use App\Item;

class AjaxController extends Controller
{
    //
    public function ajaxGetGroup() {
        $groups = Group::getGroup();
        return $groups;
    }

    public function ajaxGetHostByGroupId() {
        $groupId = (int)request('groupid', 0);
        if ($groupId == 0) {
            $hosts = Host::getAllHost();
        } else {
            $hosts = Host::getHostByGroupIds(collect([$groupId]));
        }
        return $hosts->toJson();
    }

    public function ajaxGetGraphByGroupAndHost() {
        $hostids = json_decode(request('hostid', 0));
        if (is_array($hostids)) {
            $graphs = Graph::getGraphByGroupAndHost($hostids);
        } else {
            $graphs = Graph::getGraphByGroupAndHost(collect([$hostids]));
        }

        return $graphs;
    }

    public function ajaxGetChartValueByGraphId() {
        $databaseConnection = request('databaseConnection', "");
        if ($databaseConnection == "" ) {
            return null;
        }
        $graphid = request('graphid', 0);
        $from = request('from', 0);
        $to = request('to', 2147483647);
        // dd($from);

        $GRAPH = new Graph;
        $GRAPH->setConnection($databaseConnection);
        $ITEM = new Item;
        $ITEM->setConnection($databaseConnection);

        $data = collect();
        $table = "history";
        //set orientation of legend
        $clockValue = collect();
        $firstTick = 0;
        $lastTick = 0;

        if ($graphid != 0) {
            $graph = $GRAPH->find($graphid);
            $items = $GRAPH->find($graphid)->items->sortBy('pivot_sortorder');

            if ($graph->graphtype == GRAPH_TYPE_NORMAL || $graph->graphtype == GRAPH_TYPE_STACKED) {
                //onlye show trigger in line graph
                foreach ($items as $item) {
                    list($clockValue, $firstTick, $lastTick) = getClockAndValueNumericData($item->itemid, $item->value_type, $databaseConnection, $table, $from, $to);
                    //get delay time to handle gaps data
                    if ($table == "trends") {
                        $delayTime = SEC_PER_HOUR * 1000;
                    } else {
                        $delayTime = convertToTimestamp($item->delay);
                    }
                    //add null to gaps data
                    smoothClockData($clockValue, $delayTime,true);
                    $data->push(getNewDataLine($clockValue[0], $clockValue[1]));
                }
            }
        }

        $value = null;
        for ($i = 0; $i < $data->count(); $i++) {
            $value["x"][$i] = ($data[$i]["x"]);
            $value["y"][$i] = ($data[$i]["y"]);
        }

        $result = collect([
            "data" => $value,
            "firstTick" => $firstTick,
            "lastTick" => $lastTick
        ]);

        return $result;
    }

    public function ajaxGetChartValueByItem() {
        $databaseConnection = request('databaseConnection', "");
        $itemInfos = json_decode(request('itemInfos', ""));

        if ($databaseConnection == "" || empty($itemInfos)) {
            return null;
        }

        $data = collect();
        $table = "trends";
        $clockValue = collect();
        $firstTick = 0;
        $lastTick = 0;

        // $item['source'] = ($item['trends'] == 0 || ($item['history'] > time() - ($this->from_time + $this->period / 2)
		// 			&& $this->period / $this->sizeX <= ZBX_MAX_TREND_DIFF / ZBX_GRAPH_MAX_SKIP_CELL))
        //             ? 'history' : 'trends';

        //set orientation of legend
        for ($i = 0; $i < count($itemInfos); $i++) {
            $item = Item::on($databaseConnection)->find($itemInfos[$i]->itemId);

            list($min, $max) = getRangeClock($item->itemid, $item->value_type, $databaseConnection, "history");

            $from = $itemInfos[$i]->from;
            $to = $itemInfos[$i]->to;

            $tableInfo = collect();
            if ($from < $min && $to < $min) {
                $tbl = [
                    "table" => "trends",
                    "from" => $from,
                    "to" => $to
                ];
                $tableInfo->push($tbl);
            } else if ($from < $min && $to > $min) {
                $tableInfo->push([
                    "table" => "history",
                    "from" => $min,
                    "to" => $to
                ]);
                $tableInfo->push([
                    "table" => "trends",
                    "from" => $from,
                    "to" => $min
                ]);

            } else if ($from > $min && $to > $min) {
                $tbl = [
                    "table" => "history",
                    "from" => $from,
                    "to" => $to
                ];
                $tableInfo->push($tbl);
            }

            $clockValues = collect([collect(),collect()]);
            $minCol = collect();
            $maxCol = collect();

            for ($j = 0; $j < $tableInfo->count(); $j++) {
                $tbl = $tableInfo[$j];
                // dd($tbl);
                list($clockValue, $firstTick, $lastTick) = getClockAndValueNumericData($item->itemid, $item->value_type, $databaseConnection, $tbl["table"], $tbl["from"], $tbl["to"]);
                $minCol->push($firstTick);
                $maxCol->push($lastTick);

                //get delay time to handle gaps data
                if ($tbl["table"] == "trends") {
                    $delayTime = SEC_PER_HOUR * 1000;
                } else {
                    $delayTime = convertToTimestamp($item->delay);
                }
                //add null to gaps data
                smoothClockData($clockValue, $delayTime, true, $from, $to);

                if ($j > 0 && count($clockValues[0]) > 0) {
                    if ($clockValues[0][0] != null && ($clockValues[0][0] - $clockValue[0][0] > (2 * $delayTime))) {
                        $clockValues[0]->splice(0, 0, $clockValues[0][0] + 2 * $delayTime);
                        $clockValues[1]->splice(0, 0, "null");
                    }
                }

                $clockValues[0]->splice(0, 0, $clockValue[0]->toArray());
                $clockValues[1]->splice(0, 0, $clockValue[1]->toArray());
            }

            $itemInfos[$i]->from = $minCol->min();
            $itemInfos[$i]->to = $maxCol->max();

            $data->push(getNewDataLine($clockValues[0], $clockValues[1]));
        }

        $value = null;
        for ($i = 0; $i < $data->count(); $i++) {
            $value["x"][$i] = ($data[$i]["x"]);
            $value["y"][$i] = ($data[$i]["y"]);
        }

        $result = collect([
            "data" => $value,
            "itemInfo" => $itemInfos
        ]);

        return $result;
    }

    public function ajaxGetRangeValue() {
        $databaseConnection = request('databaseConnection', "");
        if ($databaseConnection == "" ) {
            return null;
        }
        $graphid = request('graphid', 0);
        $firstTick = request('firstTick', 0);
        $lastTick = request('lastTick', 2147483647);

        $GRAPH = new Graph;
        $GRAPH->setConnection($databaseConnection);
        $ITEM = new Item;
        $ITEM->setConnection($databaseConnection);

        $minCol = collect();
        $maxCol = collect();

        if ($graphid != 0) {
            $graph = $GRAPH->find($graphid);
            $items = $GRAPH->find($graphid)->items->sortBy('pivot_sortorder');

            if ($graph->graphtype == GRAPH_TYPE_NORMAL || $graph->graphtype == GRAPH_TYPE_STACKED) {
                //onlye show trigger in line graph

                foreach ($items as $item) {
                    list($min, $max) = getRangeClock($item->itemid, $item->value_type, $databaseConnection, "history");

                    $tableInfo = collect();
                    if ($firstTick < $min && $lastTick < $min) {
                        $tbl = [
                            "table" => "trends",
                            "from" => $firstTick,
                            "to" => $lastTick
                        ];
                        $tableInfo->push($tbl);
                    } else if ($firstTick < $min && $lastTick > $min) {
                        $tableInfo->push([
                                "table" => "trends",
                                "from" => $firstTick,
                                "to" => $min
                            ]);
                        $tableInfo->push([
                                "table" => "history",
                                "from" => $min,
                                "to" => $lastTick
                            ]);
                    } else if ($firstTick > $min && $lastTick > $min) {
                        $tbl = [
                            "table" => "history",
                            "from" => $firstTick,
                            "to" => $lastTick
                        ];
                        $tableInfo->push($tbl);
                    }

                    foreach($tableInfo as $tbl) {
                        list($min, $max) = getRangeValue($item->itemid, $item->value_type, $databaseConnection, $tbl["table"], $tbl["from"], $tbl["to"]);
                        $minCol->push($min);
                        $maxCol->push($max);
                    }
                }
            }
        }

        $result = collect([
            "min" => $minCol->min(),
            "max" => $maxCol->max(),
        ]);

        return $result;
    }
}
