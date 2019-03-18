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
        var layout = {!!$layout!!}
        console.log("data old: ", data);
        // layout.width = 1000;
        // layout.height = 800;
        var myDiv = document.getElementById('plotid');
        Plotly.plot(myDiv, data, layout).then(gd => {
            gd.on('plotly_legendclick', () => false)
        });

        var updateTracert = new Array();
        for (var i = 0; i < data.length; i++) {
            updateTracert.push(i);
        }

        var requestId = {{$rq_graphid}};

        if (requestId > 0) {
            var itemIds = {{getListItemIdByGraphId($rq_graphid, getGlobalDatabaseConnection())}};
            var lengthItemIds = itemIds.length;
            var from = [];
            var to = [];
            var itemInfos = [];
            for (var i = 0; i < lengthItemIds; i++) {
                var itemInfo = {};
                itemInfo.itemId = itemIds[i];
                itemInfo.from = {{$to}};
                itemInfo.to = Math.floor(Date.now() / 1000)
                itemInfos.push(itemInfo);
            }

            var interval = setInterval(function() {
                for (var i = 0; i < itemInfos.length; i++) {
                    itemInfos[i].to = Math.floor(Date.now() / 1000)
                }
                $.ajax({
                    type: 'GET',
                    url: '/ajax/chart/item',
                    data: {
                        "databaseConnection": "{{getGlobalDatabaseConnection()}}",
                        "itemInfos": JSON.stringify(itemInfos),
                    },
                    dataType: 'json',
                    success: function (res) {
                        console.log("res: ", res);
                        console.log("from: ", itemInfos[0].from);
                        console.log("to: ", itemInfos[0].to);
                        console.log("-----");
                        // console.log("data: ", data);
                        for (var i = 0; i < res.x.length; i++) {
                            if (res.x[i].length != 0) {
                                itemInfos[i].from = itemInfos[i].to;
                            }
                        }

                        Plotly.extendTraces(myDiv, res, updateTracert);
                    },
                    error: function (error) {
                        console.log('Error:', error);
                    }
                });
            }, 5000);
        }

        function extendTraces() {
             Plotly.extendTraces(myDiv, res, updateTracert);
        }

        $yAxisPosition = parseInt($('tspan').attr('y'),10) + 50;
        /*if ({{$graphtype}} != 2) {
            document.getElementsByClassName('legend')[0].setAttribute("transform", "translate(50," + ($yAxisPosition)  +")");
        }*/

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
