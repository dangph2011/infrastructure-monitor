<?php

namespace App\Http\Controllers;

use App\Graph;
use App\Group;
use App\Host;
use App\LocalServer;
use App\Item;

class GraphController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view()
    {
        $requestLocalId = request('localid', "");

        $localServers = LocalServer::all();

        if ($localServers->isEmpty()) {
            return view('locals.blank');
        }

        if ($requestLocalId == "") {
            $firstLocalServer = LocalServer::first();
            $requestLocalId = $firstLocalServer->id;
        }

        //create connection if connection doesn't exists
        $databaseConnection = LocalServer::find($requestLocalId)->database;
        $config = getDatabaseConnection($databaseConnection);

        if (!$config) {
            createDatabaseConnectionByDatabaseName($databaseConnection, $databaseConnection);
        }
        setGlobalDatabaseConnection($databaseConnection);

        $GROUP = (new Group)->setConnection($databaseConnection);
        $HOST = (new Host)->setConnection($databaseConnection);;
        $GRAPH = (new Graph)->setConnection($databaseConnection);;
        $ITEM = (new Item)->setConnection($databaseConnection);;

        $groupids = collect();
        $hostids = collect();
        // $graphid = 0;

        $groups = $GROUP->getGroup();

        //Get groupId request
        $rq_groupid = request('groupid', 0);
        $rq_hostid = request('hostid', 0);
        $rq_graphid = request('graphid', 0);

        if ($rq_groupid == 0) {
            $groups->each(function ($group) use ($groupids) {
                $groupids->push($group->groupid);
            });
        } else {
            $groupids->push($rq_groupid);
        }

        //get hosts based on selected group
        $hosts = $HOST->getHostByGroupIds($groupids);

        if ($rq_hostid == 0) {
            $hosts->each(function ($host) use ($hostids) {
                $hostids->push($host->hostid);
            });
        } else {
            $hostids->push($rq_hostid);
        }

        //get graphs based on selected group and host
        $graphs = $GRAPH->getGraphByGroupAndHost($hostids);

        //get items based on selected graph
        $data = collect();
        $layout = collect();
        $itemInfos = collect();
        $graphtype = 0;

        if ($rq_graphid != 0) {
            $to = getLastDataTickerTimeFromGraph($rq_graphid, $databaseConnection);
            if ($to > 0) {
                $from = $to - 86400;
                $graph = Graph::on(getGlobalDatabaseConnection())->find($rq_graphid);
                $graphtype = $graph->graphtype;
                list($data, $layout, $itemInfos) = getDataAndLayoutFromGraph($rq_graphid, $databaseConnection, $from, $to);
            }
            // list($data, $layout) = getDataAndLayoutFromGraph($rq_graphid, $databaseConnection);
        }
        // dd($graphtype);
        // return $layout;
        // return $data;
        // return $itemInfos;
        return view('graphs.view', compact('groups', 'hosts', 'rq_groupid', 'graphs', 'rq_hostid', 'data', 'layout', 'rq_graphid',
                        'localServers', 'requestLocalId', 'graphtype', 'itemInfos'));
    }

    public function create()
    {
        $groupids = collect();
        $hostids = collect();
        // $graphid = 0;

        $groups = Group::getGroup();

        //Get groupId request
        $rq_groupid = request('groupid', 0);
        $rq_hostid = request('hostid', 0);
        // $rq_graphid = request('graphid', 0);

        if ($rq_groupid == 0) {
            $groups->each(function ($group) use ($groupids) {
                $groupids->push($group->groupid);
            });
        } else {
            $groupids->push($rq_groupid);
        }

        //get hosts based on selected group
        $hosts = Host::getHostByGroupIds($groupids);

        if ($rq_hostid == 0) {
            $hosts->each(function ($host) use ($hostids) {
                $hostids->push($host->hostid);
            });
        } else {
            $hostids->push($rq_hostid);
        }

        return view('graphs.create', compact('groups', 'hosts', 'rq_groupid', 'rq_hostid'));
    }
}
