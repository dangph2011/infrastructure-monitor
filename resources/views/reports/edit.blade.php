@extends('layouts.main')
@section('page-header') Report Page
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="{{asset('/js/multiselect.js')}}"></script>
@endsection

@section('contents')

<div class="container-fluid">
    <form method="POST" action="{{ route('report.update', $report->reportid) }}">
        @method('PUT') @csrf
        <div class="row">
            <div class='col-md-2'>
            </div>
            <div class='col-md-4'>
                <div class="form-group">
                    <label for="">Nhóm</label>
                    <select class="form-control" name="groupid" id="groupid">
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
                    <select class="form-control" name="hostid" id="hostid">
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
            <div class='col-md-2'>
            </div>
        </div>

        <div class="row">
            <div class='col-md-3'>
            </div>
            <div class='col-md-6'>

                <div class="form-group">
                    <label for="name">Name*</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{$report->name}}">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" name="description" id="description" value="{{$report->description}}">
                </div>

                <div class="form-group">
                    <label for="graph">Graph*</label>
                </div>

            </div>
            <div class='col-md-3'>
            </div>
        </div>

        <div class="row">
            <div class='col-md-2'>
            </div>
            <div class='col-md-8'>
                <div class="col-sm-5">
                    <select name="from[]" id="lstview" class="form-control formcls" size="12" multiple="multiple">
                        @foreach($graphFrom as $graph)
                            <option value="{{ $graph->graphid }}"> {{ $graph->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-2">
                    <button type="button" id="lstview_undo" class="btn btn-danger btn-block">undo</button>
                    <button type="button" id="lstview_rightAll" class="btn btn-primary btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                    <button type="button" id="lstview_rightSelected" class="btn btn-success btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                    <button type="button" id="lstview_leftSelected" class="btn btn-success btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                    <button type="button" id="lstview_leftAll" class="btn btn-primary btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                    <button type="button" id="lstview_redo" class="btn btn-warning btn-block">redo</button>
                </div>

                <div class="col-sm-5">
                    <select name="to[]" id="lstview_to" class="form-control formcls" size="12" multiple="multiple">
                        @foreach($graphTo as $graph)
                            <option value="{{ $graph->graphid }}"> {{ $graph->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class='col-md-2'>
                </div>
            </div>
        </div>

        <br>
        <div style="text-align: center;"><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
    @include('layouts.error')
</div>

{{--
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="js/multi-select/multiselect.js"></script> --}}

<script>
    $(document).ready(function($) {
        $("#lstview").multiselect();
    });

    $('select[name="groupid"]').on('change', function() {
        var groupId = $(this).val();
        //rebuild host select option
        $.ajax({
            type: 'GET',
            url: '/ajax/host',
            data: {"groupid": groupId},
            dataType: 'json',
            success: function (data) {
                $('select[name="hostid"]').empty();
                $('select[name="hostid"]').append('<option value="0">Tất cả</option>');
                var hostids = new Array();
                $.each(data, function(key, host) {
                    $('select[name="hostid"]').append('<option value="'+ host.hostid +'">'+ host.name +'</option>');
                    hostids.push(host.hostid);
                });

                //update list view after get group
                ajaxUpdateListView(hostids)
            },
            error: function (error) {
                console.log('Error:', error);
            }
        });
    });

    $('select[name="hostid"]').on('change', function() {
        var hostid = parseInt($(this).val());
        if (hostid != 0) {
            ajaxUpdateListView(hostid);
        } else {
            var hostids = new Array();
            $("#hostid option").each(function()
            {
                hostids.push(parseInt($(this).val()));
            });
            ajaxUpdateListView(hostids);
        }
    });

    function ajaxUpdateListView(hostids) {
        var from = new Array();
        var to = new Array();

        $("#lstview option").each(function()
        {
            from.push(parseInt($(this).val()));
        });

        $("#lstview_to option").each(function()
        {
            to.push(parseInt($(this).val()));
        });

        //rebuild graph select option
        $.ajax({
            type: 'GET',
            url: '/ajax/graph',
            data: {"hostid": JSON.stringify(hostids)},
            dataType: 'json',
            success: function (data) {
                $('select[name="from[]"]').empty();
                $.each(data, function(key, graph) {
                    if (!to.includes(graph.graphid)) {
                        $('select[name="from[]"]').append('<option value="'+ graph.graphid +'">'+ graph.name +'</option>');
                    }
                });
            },
            error: function (error) {
                console.log('Error:', error);
            }
        });
    }

</script>
</div>
@endsection
