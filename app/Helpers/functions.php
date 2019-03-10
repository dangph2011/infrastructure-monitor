<?php

use League\Flysystem\Config;

function getSelectorOption($options)
{
    $selector = collect();
    foreach ($options as $key => $value) {
        if ($value == "1m") {
            $selector->push(createSelectorOption('month', 'backward', 1, '1m'));
        } elseif ($value == "3m") {
            $selector->push(createSelectorOption('month', 'backward', 3, '3m'));
        } elseif ($value == "6m") {
            $selector->push(createSelectorOption('month', 'backward', 6, '6m'));
        } elseif ($value == "ytd") {
            $selector->push(createSelectorOption('year', 'todate', 1, 'YTD'));
        } elseif ($value == "1y") {
            $selector->push(createSelectorOption('year', 'backward', 1, '1y'));
        }
    }
    $selector->push(['step' => 'all']);
    return $selector;
}

function createSelectorOption($step, $stepMode, $count, $lable)
{
    return collect([
        "step" => $step,
        "stepmode" => $stepMode,
        "count" => $count,
        "label" => $lable,
    ]);
}

function createDataLine($x_data, $y_data, $mode, $name = null, $connectgaps = true, $size = null)
{
    return collect([
        "x" => $x_data,
        "y" => $y_data,
        "mode" => $mode,
        "name" => $name,
        "connectgaps" => $connectgaps,
        "line" => ["width" => $size],
        // "type" => 'scatter',
    ]);
}

function createXAxisLayoutLine($type = null, $title = null, $autorange = true, $rangeselector = null, $rangeslider = null)
{
    return collect([
        "autorange" => $autorange,
        "type" => $type,
        "title" => $title,
        "rangeslider" => $rangeslider,
        "rangeselector" => $rangeselector,
    ]);
}

function createYAxisLayoutLine($type = null, $title = null, $autorange = true)
{
    return collect([
        "autorange" => $autorange,
        "type" => $type,
        "title" => $title,
    ]);
}

function createLayoutLine($xaxis = null, $yaxis = null, $title = null)
{
    return collect([
        "title" => $title,
        "xaxis" => $xaxis,
        "yaxis" => $yaxis,
    ]);
}

function createLayoutTitle($title = null)
{
    return collect([
        "title" => $title,
    ]);
}

function createDataPie($value, $lable)
{
    return collect([
        "values" => $value,
        "labels" => $lable,
        "type" => 'pie',
    ]);
}

function createDataStacked($x_data, $y_data, $stackgroup, $groupnorm, $connectgaps = true)
{
    return collect([
        "x" => $x_data,
        "y" => $y_data,
        "connectgaps" => $connectgaps,
        "stackgroup" => $stackgroup,
        "groupnorm" => $groupnorm,
    ]);
}

function smoothClockData($clockValue, $delayTime)
{
    //add null to missing data
    $timestamp = 0;
    foreach ($clockValue[0] as $key => $clock) {
        if ($key != 0) {
            if ($clockValue[0][$key] - $timestamp > (2 * $delayTime)) {
                $clockValue[0]->splice($key, 0, $timestamp + $delayTime);
                $clockValue[1]->splice($key, 0, "null");
                $key++;
            }
        }
        $timestamp = $clockValue[0][$key];
    }
}

function getDataAndLayoutFromGraph($graphid)
{
    $data = collect();
    $layout = collect();
    if ($graphid != 0) {
        $graph = \App\Graph::find($graphid);
        $items = \App\Graph::find($graphid)->items;
        if ($graph->graphtype == GRAPH_TYPE_NORMAL) {
            $items->each(function ($item) use ($data, $graphid) {
                //get data
                $clockValue = getClockAndValueNumericData($item->itemid, $item->value_type);
                //get delay time to handle gaps data
                $delayTime = \App\Item::convertToTimestamp($item->delay);
                //add null to gaps data
                smoothClockData($clockValue, $delayTime);

                $data->push(createDataLine($clockValue[0], $clockValue[1], "line", $item->name, false, 1.5));
            });
            //Draw line graph

            $rangeslider = collect();
            $rangeselector = collect(['buttons' => getSelectorOption(['1m', '3m', '6m', 'ytd', '1y'])]);
            $layout = createLayoutLine(
                createXAxisLayoutLine('date', 'Date', true, $rangeselector, $rangeslider),
                createYAxisLayoutLine(null, 'Value', true),
                $graph->name
            );

        } elseif ($graph->graphtype == GRAPH_TYPE_STACKED) {
            //Draw stacked (area chart)
            $items->each(function ($item) use ($data, $graphid) {
                //get data
                $clockValue = getClockAndValueNumericData($item->itemid, $item->value_type, 'trends');
                //get delay time to handle gaps data
                $delayTime = \App\Item::convertToTimestamp($item->delay);
                //add null to gaps data
                smoothClockData($clockValue, $delayTime);
                //create data stacked
                $data->push(createDataStacked($clockValue[0], $clockValue[1], "one", "percent"));
            });
            //Draw line graph
            $rangeslider = collect();
            $rangeselector = collect(['buttons' => getSelectorOption(['1m', '3m', '6m', 'ytd', '1y'])]);
            $layout = createLayoutLine(
                createXAxisLayoutLine('date', 'Date', true, $rangeselector, $rangeslider),
                createYAxisLayoutLine(null, 'Value', true),
                $graph->name
            );
        } elseif ($graph->graphtype == GRAPH_TYPE_PIE) {
            //Draw pie graph
            //Get total of pie
            $value = collect();
            $label = collect();
            $items->each(function ($item) use ($value, $label) {
                $clockValue = getClockAndValueNumericData($item->itemid, $item->value_type);
                if ($item->pivot->type == 2) {
                    $value->prepend($clockValue[1]->avg());
                    $label->prepend($item->name);
                } else {
                    $value->push($clockValue[1]->avg());
                    $label->push($item->name);
                }
                //get delay time to handle gaps data
            });
            $value[0] -= $value->slice(1)->sum();

            $data->push(createDataPie($value, $label));

            $layout = createLayoutTitle($graph->name);

        } elseif ($graph->graphtype == GRAPH_TYPE_EXPLODED) {
            //Draw exploded graph
        }
    }

    return array($data, $layout);
}

// max clock 2147483647 03:14:07 UTC on 19 January 2038 like Y2K
function getClockAndValueNumericData($itemid, $data_type, $table = 'history', $min_clock = 0, $max_clock = 2147483647)
{
    // $table = 'history';
    if ($data_type == ITEM_VALUE_TYPE_UNSIGNED) {
        $table .= '_uint';
    }

    $tableData = DB::connection('zabbix')->table($table)->where('itemid', $itemid)->where('clock', ">=", $min_clock)
        ->where('clock', "<", $max_clock)->orderBy('clock')->get();
    $xData = collect();
    $yData = collect();
    if (starts_with($table, 'history')) {
        $tableData->each(function ($history) use ($xData, $yData) {
            //multiple 1000,  miliseconds in JS
            $xData->push($history->clock * 1000);
            $yData->push($history->value);
        });
    } else if (starts_with($table, 'trends')) {
        $tableData->each(function ($history) use ($xData, $yData) {
            //multiple 1000,  miliseconds in JS
            $xData->push($history->clock * 1000);
            $yData->push($history->value_avg);
        });
    }
    return collect([$xData, $yData]);
}

function getLocalServerSchema() {
    $schemas = DB::connection('information')->table('schemata')->where('SCHEMA_NAME', 'like', '%'.'zabbix'.'%')->get();
    return $schemas;
}

function createDatabaseConnection($key, $driver, $host, $port, $database, $username, $password, $charset = 'utf8mb4', $collation='utf8mb4_unicode_ci', $engine = null) {
    $config = Config::get('database.connections.' . $key);
    $config['driver'] = $driver;
    $config['host'] = $host;
    $config['port'] = $port;
    $config['database'] = $database;
    $config['username'] = $username;
    $config['password'] = $password;
    $config['charset'] = $charset;
    $config['collation'] = $collation;
    $config['engine'] = $engine;
    Config::set('database.connections.' . $key, $config);
}

function purgeDatabaseConnection($name = null) {
    DB::purge($name);
}


