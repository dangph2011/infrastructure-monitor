@extends('layouts.main')
@section('page-header') Report Page
@endsection

@section('scripts')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
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
                <div class="form-group">
                    <label class="form-check-label col-sm-3 text-right">Allow manual close</label>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" name="manual_close" id="manual_close" value="1">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
