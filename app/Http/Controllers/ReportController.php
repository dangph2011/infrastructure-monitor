<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Graph;
use App\Group;
use App\Host;
use App\Item;

class ReportController extends Controller
{
    //
    public function create() {
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
        // $rq_graphid = request('graphid', 0);

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

        return view('reports.create', compact('rq_groupid', 'rq_hostid', 'hosts', 'groups'));
    }

    public function view() {
        return view('reports.create');
    }
}
