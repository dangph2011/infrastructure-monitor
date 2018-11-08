<?php

namespace App\Http\Controllers;

use App\Group;
use App\Host;
use App\Trigger;
use Illuminate\Http\Request;

class TriggerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function view()
    {
        //return view('problem', compact());
        $groupid = collect();
        $hostid = collect();
        $triggers = collect();
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
        $rq_show_triggers = request('show_triggers', 3);

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

        //Get all trigger by Group and Host
        if (TRIGGERS_OPTION_ALL == $rq_show_triggers) {
            $triggers = Trigger::whereIN('flags', [0, 4])->where('status', 0)->whereHas('items', function ($query) {
                    $query->whereHas('host', function ($query) {
                        $query->where('status', 0)->where('maintenance_status', '<>', 1);
                    });
                })->orderBy('lastchange')->with('items.host')->get();
        } else if (TRIGGERS_OPTION_IN_PROBLEM == $rq_show_triggers) {
            $triggers = Trigger::whereIN('flags', [0, 4])->where('status', 0)->where('value', TRIGGER_VALUE_TRUE)
                ->whereHas('items', function ($query) {
                    $query->whereHas('host', function ($query) {
                        $query->where('status', 0)->where('maintenance_status', '<>', 1);
                    });
                })->orderBy('lastchange')->with('items.host')->get();
        }

        $config = \App\Config::first();
        // return $triggers->toJson();
        // $host =
        return view('problems.trigger', compact('groups', 'hosts', 'rq_groupid', 'rq_hostid', 'triggers', 'rq_show_triggers', 'config'));
    }

    public function create() {
        return view('problems.trigger_create');
    }
}
