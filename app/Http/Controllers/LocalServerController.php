<?php

namespace App\Http\Controllers;

use App\LocalServer;
use Illuminate\Http\Request;

class LocalServerController extends Controller
{
    private $pagination = 5;
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
        $localServers = LocalServer::paginate($this->pagination);
        $pagination = $this->pagination;
        return view('locals.index', compact('localServers', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $schemas = getLocalServerSchema();
        return view('locals.create', compact('schemas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
        $this->validate(request(),[
           'host' => 'required|unique:local_servers|ip',
           'name' => 'required|max:64',
           'database' => 'unique:local_servers'
        ]);

        $localServer = new LocalServer;
        $localServer->name = request('name', "");
        $localServer->host = request('host', "");
        $localServer->description = request('description', "");
        $localServer->note = request('note', "");
        $localServer->database = request('database', "");
        $localServer->save();
        return redirect('/local');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LocalServer  $localServer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $local = LocalServer::find($id);

        // return view('local.show', compact('local'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LocalServer  $localServer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $schemas = getLocalServerSchema();
        $local = LocalServer::find($id);
        return view('locals.edit', compact('local', 'schemas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LocalServer  $localServer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(),[
            'host' => 'required|ip|unique:local_servers,host,' .$id,
            'name' => 'required|max:64',
            'database' => 'unique:local_servers,database,' .$id
        ]);

        $localServer = LocalServer::find($id);
        $localServer->name = request('name', "");
        $localServer->host = request('host', "");
        $localServer->description = request('description', "");
        $localServer->note = request('note', "");
        $localServer->database = request('database', "");

        $localServer->save();
        return redirect('/local');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LocalServer  $localServer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $local = LocalServer::findOrFail($id);

        $local->delete();

        // redirect
        return redirect()->route('local.index')
            ->with('flash_message',
             'Successfully deleted the local name ' . $local->name);
    }
}
