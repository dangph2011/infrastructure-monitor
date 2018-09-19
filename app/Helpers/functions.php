<?php

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

function createDataLine($x_data, $y_data, $mode, $name=null, $connectgaps=true, $size = null)
{
    return collect([
        "x"=>$x_data,
        "y"=>$y_data,
        "mode" => $mode,
        "name" => $name,
        "connectgaps" => $connectgaps,
        "line" => ["width" => $size],
        // "type" => 'scatter',
    ]);
}

function createXAxisLayoutLine($type = null, $title = null, $autorange = true, $rangeselector=null, $rangeslider=null)
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

function createDataPie($x_data, $y_data, $mode, $name=null, $connectgaps=true, $size = null)
{
    return collect([
        "values"=>$x_data,
        "lables"=>$y_data,
        "type" => 'pie',
    ]);
}
