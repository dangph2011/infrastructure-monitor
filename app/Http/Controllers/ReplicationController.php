<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;
use App\DumpHistory;
use Illuminate\Support\Facades\Redirect;
use App\Replication;
use App\ReplicationLog;
use Illuminate\Support\Facades\DB;


class ReplicationController extends Controller
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
        $replogs = ReplicationLog::all();
        return view('replications.index', compact('replogs'));
    }

    public function history()
    {
        $repls = Replication::all();
        return view('replications.history', compact('repls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dumps = DumpHistory::all();
        return view('replications.create', compact('dumps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(),[
            'host' => 'required|ip',
            'port' => 'required',
            'user' => 'required',
            'password' => 'required',
            'database' => 'required',
            'slave_account' => 'required',
            'slave_password' => 'required',
            'channel' => 'required'
        ]);

        $host = request('host', "");
        $port = request('port', "");
        $user = request('user', "");
        $password = request('password', "");
        $database = request('database', "");
        $slave_account = request('slave_account', "");
        $slave_password = request('slave_password', "");
        $user_local = env('DB_USERNAME', 'forge');
        $pass_local = env('DB_PASSWORD', 'forge');
        $host_local = env('DB_HOST', '127.0.0.1');
        $port_local = env('DB_PORT', '3306');
        $channel = request('channel', "");

        // dd($host, $user, $password, $client_name, $server_name);
        $exe = 'sh script/replication_binlog.sh ' . $host .' '. $port .' '.$user . ' ' . $password . ' '
                    . $database . ' ' . $slave_account . ' ' . $slave_password . ' ' . $user_local . ' '
                        . $pass_local . ' ' . $host_local . ' ' . $port_local . ' ' . $channel;
        // dd($exe);
        $process = new Process($exe);
        $process->setTimeout(3600);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $repl = new Replication;
        $repl->host = $host;
        $repl->user = $slave_account;
        $repl->channel = $channel;
        $repl->log_text = $output;
        $repl->save();

        return redirect('replication');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $replogs = ReplicationLog::find($id);
        $channelName = $replogs->CHANNEL_NAME;
        $query = "SHOW SLAVE STATUS FOR CHANNEL " . "'" . $channelName .  "'";
        $status = collect(DB::select(DB::raw($query)));
        $status->transform(function ($item) {
            return (array)$item; // This should work in your case
        });
        // dd($status);
        return view('replications.show', compact('status'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
