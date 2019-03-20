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
                    $delayTime = convertToTimestamp($item->delay);
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
        $table = "history";
        $clockValue = collect();
        $firstTick = 0;
        $lastTick = 0;
        //set orientation of legend
        for ($i = 0; $i < count($itemInfos); $i++) {
            $item = Item::on($databaseConnection)->find($itemInfos[$i]->itemId);
            $from = $itemInfos[$i]->from;
            $to = $itemInfos[$i]->to;

            list($clockValue, $firstTick, $lastTick) = getClockAndValueNumericData($item->itemid, $item->value_type, $databaseConnection, $table, $from, $to);
            $itemInfos[$i]->from = $firstTick;
            $itemInfos[$i]->to = $lastTick;

            //get delay time to handle gaps data
            $delayTime = convertToTimestamp($item->delay);
            //add null to gaps data
            smoothClockData($clockValue, $delayTime, true, $from, $to);
            $data->push(getNewDataLine($clockValue[0], $clockValue[1]));
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
}
