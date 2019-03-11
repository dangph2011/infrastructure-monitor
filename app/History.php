<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class History extends Model
{
    //

    protected $table = 'history';
    // protected $connection = 'zabbix';

    // public function getClockAttribute()
    // {
    //     return "x:" . $this->attributes['clock'];
    // }

    // public function getValueAttribute()
    // {
    //     return "y:" . $this->attributes['value'];
    // }

    // public function getClockValueAttribute()
    // {
    //     return "x:" . $this->attributes['clock'] . "," . "y:" . $this->attributes['value'];
    // }

    public function getClockAndValueData(int $itemid)
    {
        $histories = $this::where('itemid', $itemid)->orderBy('clock')->get();
        $x_data = collect();
        $y_data = collect();
        $histories->each(function ($history) use ($x_data, $y_data) {
            //multiple 1000,  miliseconds in JS
            $x_data->push($history->clock*1000);
            $y_data->push($history->value);
        });
        return collect([$x_data, $y_data]);
    }
}
