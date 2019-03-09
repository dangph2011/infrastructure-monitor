<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplicationLog extends Model
{
    // not using int primary key
    public $incrementing = false;
    protected $primaryKey = 'CHANNEL_NAME';
    protected $fillable = ['CHANNEL_NAME', 'SERVICE_STATE', 'LAST_ERROR_MESSAGE', 'HOST', 'PORT', 'USER'];
}
