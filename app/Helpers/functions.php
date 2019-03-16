<?php

use App\Graph;
use App\Item;
use App\Trigger;
use App\Macros\CMacrosResolverHelper;
use Illuminate\Support\Facades\DB;

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

function createDataLine($x_data, $y_data, $mode, $name = null, $connectgaps = true, $size = null, $color = null, $dash = "solid")
{
    return collect([
        "x" => $x_data,
        "y" => $y_data,
        "mode" => $mode,
        "name" => $name,
        "connectgaps" => $connectgaps,
        "line" => [
            "width" => $size,
            "color" => $color,
            "dash" => $dash
        ],
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

function createYAxisLayoutLine($type = null, $title = null, $ticksuffix = "", $autorange = true)
{
    return collect([
        "autorange" => $autorange,
        "type" => $type,
        "title" => $title,
        "ticksuffix" => ' ' . $ticksuffix,
        "exponentformat" => "B"
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

function createTriggerShape($items, $databaseConnection) {
    $config = App\Config::on($databaseConnection)->first();
    $shapes = collect();
    foreach ($items as $item) {
        // $db_triggers = DBselect(
        //     'SELECT DISTINCT h.host,tr.description,tr.triggerid,tr.expression,tr.priority,tr.value'.
        //     ' FROM triggers tr,functions f,items i,hosts h'.
        //     ' WHERE tr.triggerid=f.triggerid'.
        //         " AND f.name IN ('last','min','avg','max')".
        //         ' AND tr.status='.TRIGGER_STATUS_ENABLED.
        //         ' AND i.itemid=f.itemid'.
        //         ' AND h.hostid=i.hostid'.
        //         ' AND f.itemid='.zbx_dbstr($item['itemid']).
        //     ' ORDER BY tr.priority'
        // );

        // $triggers = DB::connection(getGlobalDatabaseConnection())
        //                 ->table('triggers as tr,functions as f,items as i,hosts as h')
        //                 ->where('tr.triggerid', 'f.triggerid')
        //                 ->whereIn('f.name', collect(['last', 'min', 'avg', 'max']))
        //                 ->where('tr.status', TRIGGER_STATUS_ENABLED)
        //                 ->where('i.itemid', 'f.itemid')
        //                 ->where('h.hostid', 'i.hostid')
        //                 ->where('f.itemid', $item['itemid'])
        //                 ->select('h.host', 'tr.description', 'tr.triggerid', 'tr.expression', 'tr.priority', 'tr.value')
        //                 ->orderBy('tr.priority')
        //                 ->get();

        $triggers = DB::connection(getGlobalDatabaseConnection())
            ->table('triggers as tr')
            ->join('functions as f', 'f.triggerid', '=', 'tr.triggerid')
            ->join('items as i', 'f.itemid', '=', 'i.itemid')
            ->join('hosts as h', 'h.hostid', '=', 'i.hostid')
            ->whereIn('f.name', collect(['last', 'min', 'avg', 'max']))
            ->where('tr.status', TRIGGER_STATUS_ENABLED)
            ->where('f.itemid', $item['itemid'])
            ->select('h.host', 'tr.description', 'tr.triggerid', 'tr.expression', 'tr.priority', 'tr.value')
            ->orderBy('tr.priority')
            ->get();
        // $triggers = Trigger::on($databaseConnection)->where('status', TRIGGER_STATUS_ENABLED)
        // ->whereHas('items', function($query) use ($item) {
        //     $query->where('items.itemid',$item->itemid);
        // })->orderBy('priority')->get();

        foreach ($triggers as $trigger) {
            $trigger = get_object_vars($trigger);
            $trigger['expression'] = CMacrosResolverHelper::resolveTriggerExpressionUserMacro($trigger);
            if (!preg_match(
                '/^\{([0-9]+)\}\s*?([<>=]|[<>][=])\s*?([\-0-9\.]+)(['.ZBX_BYTE_SUFFIXES.ZBX_TIME_SUFFIXES.']?)$/',
                    $trigger['expression'], $arr)) {
                continue;
            }
            // dd($trigger);
            $constant = $arr[3].$arr[4];
            $description = CMacrosResolverHelper::resolveTriggerName($trigger) . '  ' . '['.$arr[2].' '.$constant.']';

            $shape = createShapeLayout(convert($constant), $config['severity_color_' . $trigger['priority']], $description);
            $shapes->push($shape);
        }
    }
    return $shapes;
}

function createShapeLayout($value, $color, $name, $dash = 'dot') {
    return collect([
        "name" => $name,
        "type" => "line",
        "xref" => "paper",
        "x0" => 0,
        "y0" => $value,
        "x1" => 1,
        "y1" => $value,
        "line" => createLineShape($color, 2, $dash)
    ]);
}

function createLineShape($color, $width, $type){
    return collect([
        "color" => $color,
        "width" => $width,
        "dash" => $type,
    ]);
}


function getDataAndLayoutFromGraph($graphid, $databaseConnection)
{
    $GRAPH = new Graph;
    $GRAPH->setConnection($databaseConnection);
    $ITEM = new Item;
    $ITEM->setConnection($databaseConnection);

    $data = collect();
    $layout = collect();
    $shapes = collect();
    //set orientation of legend

    if ($graphid != 0) {
        $graph = $GRAPH->find($graphid);
        $items = $GRAPH->find($graphid)->items;

        if ($graph->graphtype == GRAPH_TYPE_NORMAL) {
            //onlye show trigger in line graph
            $shapes = createTriggerShape($items, $databaseConnection);

            $items->each(function ($item) use ($data, $ITEM, $databaseConnection) {
                //get data
                $clockValue = getClockAndValueNumericData($item->itemid, $item->value_type, $databaseConnection);
                //get delay time to handle gaps data
                $delayTime = $ITEM->convertToTimestamp($item->delay);
                //add null to gaps data
                smoothClockData($clockValue, $delayTime);

                $data->push(createDataLine($clockValue[0], $clockValue[1], "lines", $item->name, false, 1.5));
            });
            //Draw line graph

            $rangeslider = collect();
            $rangeselector = collect(['buttons' => getSelectorOption(['1m', '3m', '6m', 'ytd', '1y'])]);
            $layout = createLayoutLine(
                createXAxisLayoutLine('date', null, true, $rangeselector, null),
                createYAxisLayoutLine(null, null,  $items[0]->units, true),
                $graph->name
            );
            $layout = $layout->union(setOrientedLegend(true, "v", 0, -1));

        } elseif ($graph->graphtype == GRAPH_TYPE_STACKED) {
            //Draw stacked (area chart)
            $items->each(function ($item) use ($data, $ITEM, $databaseConnection) {
                //get data
                $clockValue = getClockAndValueNumericData($item->itemid, $item->value_type, $databaseConnection, 'trends');
                //get delay time to handle gaps data
                $delayTime = $ITEM->convertToTimestamp($item->delay);
                //add null to gaps data
                smoothClockData($clockValue, $delayTime);
                //create data stacked
                $data->push(createDataStacked($clockValue[0], $clockValue[1], "one", "percent"));
            });
            //Draw line graph
            $rangeslider = collect();
            $rangeselector = collect(['buttons' => getSelectorOption(['1m', '3m', '6m', 'ytd', '1y'])]);
            $layout = createLayoutLine(
                createXAxisLayoutLine('date', null, true, $rangeselector, null),
                createYAxisLayoutLine(null, null, null, true),
                $graph->name
            );

            $layout = $layout->union(setOrientedLegend(true, "v", 0, -1));

        } elseif ($graph->graphtype == GRAPH_TYPE_PIE) {
            //Draw pie graph
            //Get total of pie
            $value = collect();
            $label = collect();
            $items->each(function ($item) use ($value, $label, $databaseConnection) {
                $clockValue = getClockAndValueNumericData($item->itemid, $item->value_type, $databaseConnection);
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

            // $layout = $layout->union(setOrientedLegend(true, "v", 0.75, 0.5));

        } elseif ($graph->graphtype == GRAPH_TYPE_EXPLODED) {
            //Draw exploded graph
        }

        if ($shapes->isNotEmpty()) {
            $data->push(createDataLine(collect([null]), collect([null]), "lines", "", false, 1.5, "FFFFFF", "dot"));
            foreach ($shapes as $shape) {
                $data->push(createDataLine(collect([null]), collect([null]), "lines", "Trigger: " .$shape['name'], false, 1.5, $shape['line']['color'], "dot"));
            }
            $layout->put("shapes", $shapes);
        }
    }

    return array($data, $layout);
}

function setOrientedLegend($showlegend, $oriented, $x, $y) {
    return collect([
        "showlegend" => $showlegend,
        "legend" => collect([
            "orientation" => $oriented,
            // "yanchor" => "bottom",
            // "xanchor" => "right",
            "x" => $x,
            "y" => $y,
        ])
    ]);
}

// max clock 2147483647 03:14:07 UTC on 19 January 2038 like Y2K
function getClockAndValueNumericData($itemid, $data_type, $databaseConnection = 'zabbix', $table = 'history', $min_clock = 0, $max_clock = 2147483647)
{
    // $table = 'history';
    if ($data_type == ITEM_VALUE_TYPE_UNSIGNED) {
        $table .= '_uint';
    }

    $tableData = DB::connection($databaseConnection)->table($table)->where('itemid', $itemid)->where('clock', ">=", $min_clock)
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

function setGlobalDatabaseConnection($databaseConnection) {
    Config::set('global.databaseConnection', $databaseConnection);
}

function getGlobalDatabaseConnection() {
    return Config::get('global.databaseConnection');
}

function createDatabaseConnectionByDatabaseName($key, $database) {
    $config = Config::get('database.connections.' . $key);
    $config['driver'] = 'mysql';
    $config['host'] = env('DB_HOST', '127.0.0.1');
    $config['port'] = env('DB_PORT', 3306);
    $config['database'] = $database;
    $config['username'] = env('DB_USERNAME', 'forge');
    $config['password'] = env('DB_PASSWORD', '');
    $config['charset'] = 'utf8mb4';
    $config['collation'] = 'utf8mb4_unicode_ci';
    $config['engine'] = null;
    Config::set('database.connections.' . $key, $config);
}

function getDatabaseConnection($key) {
    $config = Config::get('database.connections.' . $key);
    return $config;
}

function purgeDatabaseConnection($name = null) {
    DB::purge($name);
}

function zbx_toHash($value, $field = null) {
	if (is_null($value)) {
		return $value;
	}
	$result = [];

	if (!is_array($value)) {
		$result = [$value => $value];
	}
	elseif (isset($value[$field])) {
		$result[$value[$field]] = $value;
	}
	else {
		foreach ($value as $val) {
			if (!is_array($val)) {
				$result[$val] = $val;
			}
			elseif (isset($val[$field])) {
				$result[$val[$field]] = $val;
			}
		}
	}

	return $result;
}

function _s($string) {
	$arguments = array_slice(func_get_args(), 1);

	return _params(_($string), $arguments);
}

function _params($format, array $arguments) {
	return vsprintf($format, $arguments);
}

function convert($value) {
	$value = trim($value);

	if (!preg_match('/(?P<value>[\-+]?([.][0-9]+|[0-9]+[.]?[0-9]*))(?P<mult>['.ZBX_BYTE_SUFFIXES.ZBX_TIME_SUFFIXES.']?)/',
			$value, $arr)) {
		return $value;
	}

	$value = $arr['value'];
	switch ($arr['mult']) {
		case 'T':
			$value *= 1024 * 1024 * 1024 * 1024;
			break;
		case 'G':
			$value *= 1024 * 1024 * 1024;
			break;
		case 'M':
			$value *= 1024 * 1024;
			break;
		case 'K':
			$value *= 1024;
			break;
		case 'm':
			$value *= 60;
			break;
		case 'h':
			$value *= 60 * 60;
			break;
		case 'd':
			$value *= 60 * 60 * 24;
			break;
		case 'w':
			$value *= 60 * 60 * 24 * 7;
			break;
	}

	return $value;
}

