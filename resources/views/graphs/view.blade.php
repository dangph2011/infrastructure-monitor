@extends('layouts.main')
@section('page-header') Graph Page
@endsection

@section('scripts') {{--
<script src="/bower_components/moment/moment.js"></script> --}} {{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> --}}
<script src={{asset('js/plotly-latest.min.js')}}></script>
{{-- <script src="https://cdn.plot.ly/plotly-1.10.2.min.js"></script> --}}
<script src="{{asset('js/jspdf.min.js')}}"></script>
<script src="{{asset('js/html2canvas.js')}}"></script>
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <form method="GET" action="/graph">
            {{csrf_field()}}
            <div class='col-md-3'>
                <div class="form-group">
                    <label for="">{{__('Local Server')}}</label>
                    <select class="form-control" name="localid" id="localid" onchange="this.form.submit()">
                    @foreach($localServers as $localServer)
                        @if ($localServer->id == $requestLocalId)
                            <option value="{{ $localServer->id }}" selected> {{ $localServer->name }}</option>
                        @else
                            <option value="{{ $localServer->id }}" > {{ $localServer->name }}</option>
                        @endif
                    @endforeach
                </select>
                </div>
            </div>
            <div class='col-md-3'>
                <div class="form-group">
                    <label for="">{{__('Group')}}</label>
                    <select class="form-control" name="groupid" id="" onchange="this.form.submit()">
                    <option value=0>{{__('All')}}</option>
                    @foreach($groups as $group)
                        {{-- {{$rq_groupid}} --}}
                        @if ($group->groupid == $rq_groupid)
                            <option value="{{ $group->groupid }}" selected> {{ $group->name }}</option>
                        @else
                            <option value="{{ $group->groupid }}" > {{ $group->name }}</option>
                        @endif
                    @endforeach
                </select>
                </div>
            </div>
            <div class='col-md-3'>
                <div class="form-group">
                    <label for="">{{__('Host')}}</label>
                    <select class="form-control" name="hostid" id="" onchange="this.form.submit()">
                    <option value=0>{{__('All')}}</option>
                    @foreach($hosts as $host)
                        @if ($host->hostid == $rq_hostid)
                            <option value="{{ $host->hostid }}" selected> {{ $host->name }}</option>
                        @else
                            <option value="{{ $host->hostid }}"> {{ $host->name }}</option>
                        @endif
                    @endforeach
                </select>
                </div>
            </div>
            <div class='col-md-3'>
                <div class="form-group">
                    <label for="">{{__('Graph')}}</label>
                    <select class="form-control" name="graphid" id="" onchange="this.form.submit()">
                    <option value=0>{{__('All')}}</option>
                    @foreach($graphs as $graph)
                        @if ($graph->graphid == $rq_graphid)
                            <option value="{{ $graph->graphid }}" selected> {{ $graph->name }}</option>
                        @else
                            <option value="{{ $graph->graphid }}"> {{ $graph->name }}</option>
                        @endif
                    @endforeach
                </select>
                </div>
            </div>
        </form>
    </div>

    <div style="text-align: center;"><button type="button" id="savePdf" class="btn btn-primary" onclick="generatePDF()">{{__('Generate PDF')}}</button></div>
    <div style="text-align: center;"><span id="genmsg" style="display:none; color:#0000FF;"><b>{{__('Generating PDF ...')}}</b></span></div>

    <div id="plotid" class="plotid"></div>

    <script>
        var data = {!!$data!!};
        var dataLength = {{count($data)}};
        var layout = {!!$layout!!}
        layout.autosize = true;
        // layout.width = 1000;
        layout.height = 500;

        var myPlot = document.getElementById('plotid');
        Plotly.plot(myPlot, data, layout, {
                    doubleClick: false,
                    scrollZoom: true
                }).then(gd => {
                    gd.on('plotly_legendclick', () => false)
                });

        var connection = "{{getGlobalDatabaseConnection()}}";

        ajaxGetYaxisRange(connection, {{$rq_graphid}});
        setLegendPosition();

        var updateTracert = new Array();
        for (var i = 0; i < data.length; i++) {
            updateTracert.push(i);
        }

        var requestId = {{$rq_graphid}};
        var itemInfos = {!!$itemInfos!!};
        console.log("itemInfos origin: ", itemInfos);
        console.log("itemInfos origin From: ", new Date(itemInfos[0].from*1000));
        console.log("itemInfos origin to: ", new Date(itemInfos[0].to*1000));
        var itemIdsLength = itemInfos.length;

        if (requestId > 0) {
            var interval = setInterval(function() {
                var to = getUnixTime(Date.now());
                // console.log("to: ", to);
                // console.log('itemInfos:: ', itemInfos);
                var itemInfoGetData = getDataExtend(to);
                console.log('extend itemInfoGetData: ', itemInfoGetData);
                ajaxGetDataOnChart(connection, itemInfoGetData, 'extend');
                //auto scale yaxis
            }, 60000);
        }

        myPlot.on('plotly_relayout', function(data){
            console.log("data 1: ", data);
            if (data["xaxis.range[0]"] != undefined) {
                var from = getUnixTime(new Date(data["xaxis.range[0]"]).getTime());
                var itemInfoGetData = getDataPrepend(from);
                console.log("from: ", from);
                console.log("itemInfos: ", itemInfos);
                console.log("itemInfos From: ", new Date(itemInfos[0].from*1000));
                console.log("itemInfos to: ", new Date(itemInfos[0].to*1000));
                console.log('relayout itemInfoGetData: ', itemInfoGetData);
                console.log("relayout itemInfoGetData From: ", new Date(itemInfoGetData[0].from*1000));
                console.log("relayout itemInfoGetData to: ", new Date(itemInfoGetData[0].to*1000));
                // console.log('itemInfos: ', itemInfos);
                ajaxGetDataOnChart(connection, itemInfoGetData, "prepend");
            }
        });

        myPlot.on('plotly_doubleclick',  () => false);

        function ajaxGetDataOnChart(connection, itemInfoGetData, getType) {
            console.log("ajaxGetDataOnChart");
            $.ajax({
                type: 'GET',
                url: '/ajax/chart/item',
                data: {
                    "databaseConnection": connection,
                    "itemInfos": JSON.stringify(itemInfoGetData),
                },
                dataType: 'json',
                success: function (res) {
                    if (getType == 'extend') {
                        updateAfterExtendData(res);
                    } else if (getType == 'prepend') {
                        updateAfterPrependData(res);
                    }

                    ajaxGetYaxisRange(connection, {{$rq_graphid}});
                    setLegendPosition();
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        }

        function ajaxGetYaxisRange(connection, graphId, xFirstTick = 0, xLastTick = 0) {
            console.log("run: ajaxGetYaxisRange");
            if (xFirstTick == 0) {
                xFirstTick = getUnixTime(new Date(myPlot.layout.xaxis.range[0]).getTime());
            };

            if (xLastTick == 0) {
                xLastTick = getUnixTime(new Date(myPlot.layout.xaxis.range[1]).getTime());
            }

            $.ajax({
                type: 'GET',
                url: '/ajax/chart/range',
                data: {
                    "databaseConnection": connection,
                    "graphid": graphId,
                    "firstTick" : xFirstTick,
                    "lastTick" : xLastTick
                },
                dataType: 'json',
                success: function (res) {
                    console.log("aaa: ", res);
                    updateScaleYaxis(res);
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        }

        function updateScaleYaxis(res) {
            if (res.min == null || res.max == null) return;
            var update = {
                'yaxis.range': [res.min * 100/120, res.max*120/100],
                // 'yaxis.range': [0, 3000],   // updates the xaxis range
            };
            console.log("update range: ", update);
            Plotly.relayout(myPlot, update);
        }

        function setLegendPosition() {
            $yAxisPosition = parseInt($('.xtick:first > text').attr('y'),10) + 100;
            if ({{$graphtype}} != 2) {
                document.getElementsByClassName('legend')[0].setAttribute("transform", "translate(50," + ($yAxisPosition)  +")");
            }
        }

        function getDataPrepend(from) {
            var itemInfoGetData = [];
            for (var i = 0; i < itemInfos.length; i++) {
                var item = {};
                item.itemId = itemInfos[i].itemId;
                item.from = from;
                item.to = from;
                if (itemInfos[i].from > from) {
                    item.to = itemInfos[i].from;
                }
                itemInfoGetData.push(item);
            }

            return itemInfoGetData;
        }

        function getDataExtend(to) {
            // debugger;
            var itemInfoGetData = [];
            for (var i = 0; i < itemInfos.length; i++) {
                var item = {};
                item.itemId = itemInfos[i].itemId.valueOf();
                item.from = to;
                item.to = to;
                if (itemInfos[i].to < to) {
                    item.from = itemInfos[i].to.valueOf();
                }
                itemInfoGetData.push(item);
            }
            return itemInfoGetData;
        }

        function updateAfterPrependData(res) {
            var updateData = res.data;
            console.log('updateAfterPrependData : ', res);
            // console.log('Item get: ', res.itemInfo);
            // console.log('Susccess prepend traces: ', updateData);
            for (var i = 0; i < (dataLength - itemIdsLength); i++) {
                updateData.x.push([]);
                updateData.y.push([]);
            }
            for (var i = 0; i < updateData.x.length; i++) {
                if (updateData.x[i].length > 0) {
                    itemInfos[i].from = res.itemInfo[i].from;
                }
            }
            // console.log('Add null value for prepend: ', updateData);
            Plotly.prependTraces(myPlot, updateData, updateTracert);
        }

        function updateAfterExtendData(res, to) {
            var updateData = res.data;
            console.log('updateAfterExtendData : ', res);
            // console.log('Susccess: ', updateData);
            for (var i = 0; i < (dataLength - itemIdsLength); i++) {
                updateData.x.push([]);
                updateData.y.push([]);
            }
            // debugger;
            for (var i = 0; i < updateData.x.length; i++) {
                if (updateData.x[i].length > 0) {
                    itemInfos[i].to = res.itemInfo[i].to;
                }
            }
            // console.log('Infos after change: ', itemInfos);
            // console.log('Add null value for extend: ', updateData);
            Plotly.extendTraces(myPlot, updateData, updateTracert);
        }

        function getUnixTime(timeDate) {
            return Math.floor(timeDate / 1000);
        }

        function generatePDF() {
            $("#savePdf").hide();
		    $("#genmsg").show();
            var timeStamp = '{{Carbon\Carbon::now()}}';
            const filename  = 'report ' + timeStamp + '.pdf';
            html2canvas(document.querySelector('#plotid'), { allowTaint: true }).then(canvas => {
                let pdf = new jsPDF('l', 'cm', 'a4');
                var width = pdf.internal.pageSize.getWidth();
                var height = pdf.internal.pageSize.getHeight();
                var h1 = 0;
                var w1 = 0;
                var widthImage = width - w1;
                var heightImage = (canvas.height - w1) * width / canvas.width;
                var h1 = (height - heightImage)/2 ;
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', w1, h1, widthImage, heightImage);
                setTimeout(function() {
                    pdf.save(filename);
                    $("#savePdf").show();
                    $("#genmsg").hide();
                }, 0);
            });
	    }
    </script>
    </div>
@endsection
