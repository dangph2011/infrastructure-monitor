<?php

namespace App\Http\Controllers;

use App\Graph;
use App\Group;
use App\Host;
use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::all();
        return view('reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        //get graphs based on selected group and host
        $graphs = Graph::getGraphByGroupAndHost($hostids);

        return view('reports.create', compact('rq_groupid', 'rq_hostid', 'hosts', 'groups', 'graphs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
            'to' => 'required',
        ]);

        $reListView = request('to', 0);
        $description = request('description', '');

        if ($reListView != 0) {
            $report = new Report;
            $report->name = request('name');
            $report->description = $description;
            $report->save();
            //
            $graphids = collect();

            foreach ($reListView as $key => $value) {
                $graphids->push($value);
            }
            $report->saveReport($graphids);
        }
        return redirect('/report');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = Report::find($id);
        $graphs = $report->graphs;

        $reportData = collect();
        foreach ($graphs as $g) {
            $data = collect();
            $layout = collect();
            list($data, $layout) = getDataAndLayoutFromGraph($g->graphid);
            $reportData->push(['data' => $data, 'layout' => $layout]);
        }

        return view('reports.show', compact('report', 'reportData'));
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = Report::find($id);
        $graphsReport = $report->graphs;

        $graphsReportId = collect();

        foreach ($graphsReport as $gr) {
            $graphsReportId->push($gr->graphid);
        }

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

        //get graphs based on selected group and host
        $graphs = Graph::getGraphByGroupAndHost($hostids);

        $graphFrom = collect();
        $graphTo = $graphsReport;

        for ($i = 0; $i < $graphs->count(); $i++) {
            if (!$graphsReportId->contains($graphs[$i]->graphid)) {
                $graphFrom->push($graphs[$i]);
            }
        }

        return view('reports.edit', compact('rq_groupid', 'rq_hostid', 'hosts', 'groups', 'report', 'graphFrom', 'graphTo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        print_r("update");
        $this->validate(request(), [
            'name' => 'required',
            'to' => 'required',
        ]);

        $reListView = request('to', 0);
        $description = request('description', '');

        if ($reListView != 0) {
            $report = Report::find($id);
            $report->name = request('name');
            $report->description = $description;
            $report->save();
            //
            $graphids = collect();

            foreach ($reListView as $key => $value) {
                $graphids->push($value);
            }
            $report->saveReport($graphids);
        }
        return redirect('/report');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete
        $report = Report::findOrFail($id);

        $report->destroyReport();

        $report->delete();

        // redirect
        return redirect()->route('report.index')
            ->with('flash_message',
             'Successfully deleted the report id ' . $id);
    }
}
