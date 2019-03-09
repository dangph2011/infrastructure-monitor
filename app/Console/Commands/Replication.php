<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\ReplicationLog;

class Replication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replication:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write log into replication_log table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $replConfs = DB::connection('performance')->table('replication_connection_configuration')->get();
        $replStatuses = DB::connection('performance')->table('replication_connection_status')->get();
        $newChannelName = collect();
        $oldChannelName = collect();
        foreach ($replConfs as $v) {
            $newChannelName->push($v->CHANNEL_NAME);
        }

        $replLog = ReplicationLog::all();

        foreach ($replLog as $v) {
            $oldChannelName->push($v->CHANNEL_NAME);
        }

        $deleteChannel = $oldChannelName->diff($newChannelName);

        if ($deleteChannel->count() > 0) {
            ReplicationLog::destroy($deleteChannel);
        }

        foreach ($replConfs as $v) {
            ReplicationLog::updateOrCreate(
                ['CHANNEL_NAME' => $v->CHANNEL_NAME],
                [
                    'CHANNEL_NAME' => $v->CHANNEL_NAME,
                    'HOST' => $v->HOST,
                    'PORT' => $v->PORT,
                    'USER' => $v->USER,
                ]
            );
        }

        foreach ($replStatuses as $v) {
            ReplicationLog::updateOrCreate(
                ['CHANNEL_NAME' => $v->CHANNEL_NAME],
                [
                    'CHANNEL_NAME' => $v->CHANNEL_NAME,
                    'SERVICE_STATE' => $v->SERVICE_STATE,
                    'LAST_ERROR_MESSAGE' => $v->LAST_ERROR_MESSAGE,
                ]
            );
        }
    }
}
