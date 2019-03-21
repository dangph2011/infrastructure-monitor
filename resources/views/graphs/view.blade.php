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

        // layout.width = 1000;
        // layout.height = 800;
        var myPlot = document.getElementById('plotid');
        Plotly.plot(myPlot, data, layout).then(gd => {
            gd.on('plotly_legendclick', () => false)
        });

        $yAxisPosition = parseInt($('tspan').attr('y'),10) + 50;
        if ({{$graphtype}} != 2) {
            document.getElementsByClassName('legend')[0].setAttribute("transform", "translate(50," + ($yAxisPosition)  +")");
        }

        var updateTracert = new Array();
        for (var i = 0; i < data.length; i++) {
            updateTracert.push(i);
        }

        var requestId = {{$rq_graphid}};
        var itemIdsLength = 0;

        if (requestId > 0) {
            var itemIds = {{getListItemIdByGraphId($rq_graphid, getGlobalDatabaseConnection())}};
            var itemIdsLength = itemIds.length;

            var from = [];
            var to = [];
            var itemInfos = [];
            for (var i = 0; i < itemIdsLength; i++) {
                var itemInfo = {};
                itemInfo.itemId = itemIds[i];
                itemInfo.from = {{$firstTick}};
                itemInfo.to = {{$lastTick}};
                itemInfos.push(itemInfo);
            }

            var interval = setInterval(function() {
                var to = getUnixTime(Date.now());
                // console.log("to: ", to);
                // console.log('itemInfos:: ', itemInfos);
                var itemInfoGetData = getDataExtend(to);
                // console.log('itemInfoGetData: ', itemInfoGetData);

                $.ajax({
                    type: 'GET',
                    url: '/ajax/chart/item',
                    data: {
                        "databaseConnection": "{{getGlobalDatabaseConnection()}}",
                        "itemInfos": JSON.stringify(itemInfoGetData),
                    },
                    dataType: 'json',
                    success: function (res) {
                        updateAfterExtendData(res);
                    },
                    error: function (error) {
                        console.log('Error:', error);
                    }
                });
            }, 10000);
        }

        myPlot.on('plotly_relayout', function(data){
            if (data["xaxis.range[0]"] != undefined) {
                var from = getUnixTime(new Date(data["xaxis.range[0]"]).getTime());
                var itemInfoGetData = getDataPrepend(from);
                // console.log("from: ", from);
                // console.log('itemInfoGetData: ', itemInfoGetData);
                // console.log('itemInfos: ', itemInfos);

                $.ajax({
                    type: 'GET',
                    url: '/ajax/chart/item',
                    data: {
                        "databaseConnection": "{{getGlobalDatabaseConnection()}}",
                        "itemInfos": JSON.stringify(itemInfoGetData),
                    },
                    dataType: 'json',
                    success: function (res) {
                        updateAfterPrependData(res);
                    },
                    error: function (error) {
                        console.log('Error:', error);
                    }
                });
            }
        });

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
            // console.log('Infos: ', itemInfos);
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
            // console.log('Item get: ', res.itemInfo);
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
