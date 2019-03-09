<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\Host;
use App\Graph;

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
}