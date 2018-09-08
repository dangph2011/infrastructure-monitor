<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Graph;
use App\Item;
use App\Group;
use App\GraphsItem;

class GraphController extends Controller
{
    //
    public function view()
    {
        // $graph = Graph::where('flags',0)->orWhere('flags',4)->get();
        // print_r($graph);
        // return view('graphs.view');
    }
}
