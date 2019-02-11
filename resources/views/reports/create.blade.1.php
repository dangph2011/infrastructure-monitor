@extends('layouts.main')
@section('page-header') Report Page
@endsection


@section('contents')

<div class="container-fluid">
    <div class="row">
        <form method="GET" action="/report/create">
            {{csrf_field()}}
            <div>
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
            <div>
                <div class='col-md-4'>
                </div>
                <div class='col-md-4'>
                    @foreach ($graphs as $graph)
                    <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="{{$graph->graphid}}">
                            <label class="form-check-label" for="check_{{$graph->graphid}}">{{$graph->name}}</label>
                        </div>
                    @endforeach
                    <hr/>
                    <button type="button" id="add" class="btn btn-primary">Add</button>
                    <button type="button" id="preview" class="btn btn-default">Preview</button>
                    <button type="button" id="export" class="btn btn-default">Export</button>
                    <button type="button" id="cancel" class="btn btn-default">Cancel</button>
                </div>
                <div class='col-md-4'>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
