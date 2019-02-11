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
    <form method="POST" action="/report" id="createReport">
        {{csrf_field()}}
        <div class="row">
            <div class='col-md-2'>
            </div>
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
            <div class='col-md-2'>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-5">
                <select name="from" id="lstview" class="form-control formcls" size="12" multiple="multiple">
                @foreach($graphs as $graph)
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
                </select>
            </div>
        </div>

        <br>
        <div style="text-align: center;"><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>

{{--
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="js/multi-select/multiselect.js"></script> --}}

<script>
    jQuery(document).ready(function($) {
        $("#lstview").multiselect();
    });

</script>
</div>
@endsection
