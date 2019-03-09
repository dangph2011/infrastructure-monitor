<?php

namespace App\Http\Controllers;

use App\Event;
use App\Group;
use App\Host;
use App\Problem;
use Illuminate\Http\Request;
use App\EventRecovery;

class ProblemController extends Controller
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

        // $graphid = 0;

        $groups = Group::getGroup();

        //Get groupId request
        $rq_groupid = request('groupid', 0);
        $rq_hostid = request('hostid', 0);
        $rq_show_problems = request('show_problems', 3);

        if ($rq_groupid == 0) {
            $groups->each(function ($group) use ($groupid) {
                $groupid->push($group->groupid);
            });
        } else {
            $groupid->push($rq_groupid);
        }

        //get hosts based on selected group
        $hosts = Host::getHostByGroupIds($groupid);

        if ($rq_hostid == 0) {
            $hosts->each(function ($host) use ($hostid) {
                $hostid->push($host->hostid);
            });
        } else {
            $hostid->push($rq_hostid);
        }

        $config = \App\Config::first();

        $problems = ($rq_show_problems == TRIGGERS_OPTION_ALL)
        ? self::getDataEvents()
        : self::getDataProblems();

        // $problems = Problem::orderBy('clock', 'DESC')->get();
        $config = \App\Config::first();

        // print_r($problems->toJson());
        return view('problems.problem', compact('groups', 'hosts', 'rq_groupid', 'rq_hostid', 'problems', 'rq_show_problems', 'config'));
    }

    public function getDataEvents()
    {
        $events = Event::where('source', 0)
            ->where('object', 0)
            ->where('value', 1)
            ->whereHas('trigger')
            ->with('trigger', 'eventrecovery.r_event')
            // ->with('trigger')
            ->orderBy('eventid', 'DESC')
            ->get();

        foreach ($events as &$event) {
            if (isset($event->eventrecovery)) {
                $event['r_eventid'] = $event->eventrecovery->r_eventid;
                $event['r_clock'] = $event->eventrecovery->r_event->clock;
                $event['correlationid'] =  $event->eventrecovery->r_event->correlationid;
                $event['userid'] =  $event->eventrecovery->r_event->userid;
            } else {
                $event['r_eventid'] = 0;
                $event['r_clock'] = 0;
                $event['correlationid'] = 0;
                $event['userid'] = 0;
            }
        }
        return $events;
    }

    public function getDataProblems()
    {
        return Problem::where('source', 0)
            ->where('object', 0)
            ->whereNull('r_eventid')
            ->whereHas('trigger')
            ->with('trigger')
            ->orderBy('eventid', 'DESC')
            ->get();
    }
}
