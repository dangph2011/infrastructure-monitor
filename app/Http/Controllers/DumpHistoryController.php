<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DumpHistory;
use Symfony\Component\Process\Process;

class DumpHistoryController extends Controller
{
    private $pagination = 3;

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

        $dumps = DumpHistory::paginate($this->pagination);
        $pagination = $this->pagination;
        return view('dumps.index', compact('dumps', 'pagination'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('dumps.create');
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
        ]);

        $host = request('host', "");
        $port = request('port', 3306);
        $user = request('user', "");
        $password = request('password', "");
        $database = request('database', "");
        // dd($host, $user, $password, $client_name, $server_name);
        $exe = 'sh script/dump.sh ' . $host .' ' .$port . ' ' .$user . ' ' . $password . ' ' . $database;
        $process = new Process($exe);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $dumpHistory = new DumpHistory;
        $dumpHistory->log_text = $output;
        $dumpHistory->database = $database;
        $dumpHistory->host = $host;

        $separator = "\r\n";
        $line = strtok($output, $separator);

        while ($line !== false) {
            # do something with $line
            $line = strtok( $separator );

            if (strstr($line,"filename") != false) {
                $content = explode(" ", strstr($line, "filename"));
                $dumpHistory->filename = $content[1];
                // continue;
            }

            if (strstr($line,"log_file") != false) {
                $content = explode(" ", strstr($line, "log_file"));
                $dumpHistory->log_file = $content[1];
                // continue;
            }

            if (strstr($line,"log_pos") != false) {
                $content = explode(" ", strstr($line, "log_pos"));
                $dumpHistory->log_pos = $content[1];
                continue;
            }
        }

        if (!empty($dumpHistory->database) && !empty($dumpHistory->filename)
                && !empty($dumpHistory->log_file) && !empty($dumpHistory->log_pos)
                    && !empty($dumpHistory->log_text)) {
            $dumpHistory->save();
        } else {
            return Redirect::back()->withErrors(['message1'=>"Có lỗi trong quá trình tạo sao lưu CSDL"]);
        }

        return redirect('dump');
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
