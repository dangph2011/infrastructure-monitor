@extends('layouts.main')
@section('page-header') Graph Page
@endsection

@section('scripts') {{--
<script src="/bower_components/moment/moment.js"></script> --}} {{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> --}}
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script src="{{asset('js/jspdf.min.js')}}""></script>
<script src="{{asset('js/html2canvas.js')}}"></script>
@endsection


<style>
    /* Style buttons */

    .btn {
        background-color: DodgerBlue;
        border: none;
        color: white;
        padding: 12px 30px;
        cursor: pointer;
        font-size: 20px;
    }

    /* Darker background on mouse-over */

    .btn:hover {
        background-color: RoyalBlue;
    }
</style>


@section('contents')

<div class="container-fluid">
    <div class="row">
        <form method="GET" action="/graph">
            {{csrf_field()}}
            <div class='col-md-4'>
                <div class="form-group">
                    <label for="">Nhóm</label>
                    <select class="form-control" name="groupid" id="" onchange="this.form.submit()">
                    <option value=0>Tất cả</option>
                    @foreach($groups as $group)
                        {{$rq_groupid}}
                        @if ($group->groupid == $rq_groupid)
                            <option value="{{ $group->groupid }}" selected> {{ $group->name }}</option>
                        @else
                        <option value="{{ $group->groupid }}" > {{ $group->name }}</option>
                        @endif
                    @endforeach
                </select>
                </div>
            </div>
            <div class='col-md-4'>
                <div class="form-group">
                    <label for="">Máy chủ</label>
                    <select class="form-control" name="hostid" id="" onchange="this.form.submit()">
                    <option value=0>Tất cả</option>
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
            <div class='col-md-4'>
                <div class="form-group">
                    <label for="">Đồ thị</label>
                    <select class="form-control" name="graphid" id="" onchange="this.form.submit()">
                    <option value=0>Tất cả</option>
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

    <div id="plotid" class="container-fluid"></div>
    {{-- <button class="btn"><i class="fa fa-download"></i> Download</button> --}} {{-- <a href="{{ url('/graph/download' . $data . $layout) }}"
        class="btn btn-primary" role="button">Download Link</a> --}}
    <img id="jpg-export"></img>
    <div><button type="button" class="btn btn-primary btn-lg" style="float: right;" onclick="print()">Save</button></div>

    <script>
        var data = {!!$data!!};
        var layout = {!!$layout!!}
        var myDiv = document.getElementById('plotid')
        Plotly.newPlot(myDiv, data, layout);
        // print();
        // Plotly.relayout( myDiv, {
        //     'xaxis.autorange': true,
        //     'yaxis.autorange': true
        // });

        function print() {
            var timeStamp = '{{Carbon\Carbon::now()}}';
            const filename  = 'report ' + timeStamp + '.pdf';
            html2canvas(document.querySelector('#plotid')).then(canvas => {
                let pdf = new jsPDF('l', 'cm', 'a3');
                var width = pdf.internal.pageSize.getWidth();
                var height = pdf.internal.pageSize.getHeight();
                var h1 = 0;
                var w1 = 0;
                var widthImage = width - w1;
                var heightImage = (canvas.height - w1) * width / canvas.width;
                var h1 = (height - heightImage)/2 ;
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', w1, h1, widthImage, heightImage);
                pdf.save(filename);
            });
	    }
    </script>

    {{--
    <script>
        var d3 = Plotly.d3;
        var img_jpg= d3.select('#jpg-export');

        // Ploting the Graph

        var trace={x:[3,9,8,10,4,6,5],y:[5,7,6,7,8,9,8],type:"scatter"};
        var trace1={x:[3,4,1,6,8,9,5],y:[4,2,5,2,1,7,3],type:"scatter"};
        var data = [trace,trace1];
        var layout = {title : "Simple Javascript Graph"};
        Plotly.plot(
        'plotid',
        data,
        layout)

        // static image in jpg format

        .then(
            function(gd)
            {
            Plotly.toImage(gd,{height:300,width:300})
                .then(
                    function(url)
                {
                    img_jpg.attr("src", url);
                    return Plotly.toImage(gd,{format:'jpeg',height:400,width:400});
                }
                )
            });
    </script> --}}
</div>
@endsection
