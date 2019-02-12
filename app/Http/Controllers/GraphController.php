<?php

namespace App\Http\Controllers;

use App\Graph;
use App\Group;
use App\Host;
use Illuminate\Http\Request;

class GraphController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view()
    {
        $groupids = collect();
        $hostids = collect();
        // $graphid = 0;

        $groups = Group::getGroup();

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
        $hosts = Host::getHostByGroupIds($groupids);

        if ($rq_hostid == 0) {
            $hosts->each(function ($host) use ($hostids) {
                $hostids->push($host->hostid);
            });
        } else {
            $hostids->push($rq_hostid);
        }

        //get graphs based on selected group and host
        $graphs = Graph::getGraphByGroupAndHost($hostids);

        //get items based on selected graph

        // $items = Item::whereHas('graphs', function ($query) use ($rq_graphid) {
        //     $query->where('graphs.graphid', $rq_graphid);
        // })->get();

        $data = collect();
        $layout = collect();
        list($data, $layout) = getDataAndLayoutFromGraph($rq_graphid);
        // dd($tracers);
        // return $layout;
        return view('graphs.view', compact('groups', 'hosts', 'rq_groupid', 'graphs', 'rq_hostid', 'data', 'layout', 'rq_graphid'));
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
