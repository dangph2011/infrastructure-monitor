<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trend extends Model
{
    //
    public static function getClockAndValueData(int $itemid)
    {
        $histories = Trend::where('itemid', $itemid)->get();
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
