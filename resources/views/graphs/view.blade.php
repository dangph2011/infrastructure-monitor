@extends('layouts.main')
@section('page-header') Graph Page
@endsection

@section('scripts')
{{-- <script src="/bower_components/moment/moment.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> --}}
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
@endsection

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

    <script>
        var data = {!!$data!!};
        var layout = {!!$layout!!}
        var myDiv = document.getElementById('plotid')
        Plotly.newPlot(myDiv, data, layout);
    </script>
</div>
@endsection
